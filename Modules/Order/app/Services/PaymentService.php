<?php

namespace Modules\Order\Services;

use Illuminate\Support\Facades\Log;
use Modules\Order\Models\Order;
use Modules\Order\Models\Payment;
use Modules\Order\Models\PaymentLog;
use Modules\Order\Models\OrderStatus;
use Modules\Cart\Services\CartService;
use Modules\Activity\Services\AuditLogger;
use Sadad\Library\SadadLibrary;

class PaymentService
{
    public function createSadadPayment(Order $order, array $customer, string $callbackUrl): Payment
    {
        $ref = $this->buildRefNumber($order->id);

        $payment = Payment::create([
            'order_id' => $order->id,
            'ref_number' => $ref,
            'provider' => 'sadad',
            'amount' => $order->grand_total,
            'currency' => $order->currency,
            'status' => 'pending',
            'callback_url' => $callbackUrl,
        ]);

        $sadadConfig = [
            'clientId' => config('order.payment.sadad.client_id'),
            'clientSecret' => config('order.payment.sadad.client_secret'),
            'isTest' => (bool) config('order.payment.sadad.is_test', true),
        ];

        $sadad = new SadadLibrary($sadadConfig);
        $sadad->generateRefreshToken();

        $invoice = [
            'ref_Number' => $ref,
            'amount' => SadadLibrary::getKWDAmount($order->currency, $order->grand_total),
            'customer_Name' => $customer['name'] ?? '',
            'customer_Mobile' => SadadLibrary::validatePhone($customer['mobile'] ?? ''),
            'customer_Email' => $customer['email'] ?? '',
            'currency_Code' => $order->currency,
            'callback_url' => $callbackUrl,
        ];

        $requestArray = ['Invoices' => [$invoice]];

        $this->log($payment, 'request', $requestArray);

        try {
            $sadadInvoice = $sadad->createInvoice($requestArray, $sadad->refreshToken);

            $this->log($payment, 'response', $sadadInvoice);

            if (! isset($sadadInvoice['InvoiceURL'])) {
                $payment->update(['status' => 'failed']);
                Log::error('Sadad invoice creation failed: Invoice URL missing', [
                    'payment_id' => $payment->id,
                    'order_id' => $order->id,
                    'response' => $sadadInvoice,
                ]);

                throw new \RuntimeException(__('order::messages.invoice_url_missing'));
            }
        } catch (\Exception $e) {
            $payment->update(['status' => 'failed']);
            $this->log($payment, 'error', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
            Log::error('Sadad payment creation failed', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException(__('order::messages.payment_creation_failed'));
        }

        $payment->update([
            'invoice_url' => $sadadInvoice['InvoiceURL'],
            'payload' => $sadadInvoice,
        ]);

        return $payment;
    }

    public function handleCallback(array $payload): ?Order
    {
        $ref = $payload['ref_Number'] ?? $payload['ref_number'] ?? null;
        $invoiceId = $payload['invoice_id'] ?? $payload['InvoiceId'] ?? $payload['invoiceId'] ?? null;

        if (! $ref && ! $invoiceId) {
            Log::warning('Payment callback received without ref_number or invoice_id', ['payload' => $payload]);

            return null;
        }

        $paymentQuery = Payment::query();

        if ($ref) {
            $paymentQuery->where('ref_number', $ref);
        } elseif ($invoiceId) {
            $paymentQuery->where('payload->InvoiceId', $invoiceId);
        }

        $payment = $paymentQuery->first();
        if (! $payment) {
            Log::warning('Payment callback received for unknown payment', [
                'ref' => $ref,
                'invoice_id' => $invoiceId,
                'payload' => $payload,
            ]);

            return null;
        }

        $this->log($payment, 'webhook', $payload, 200);

        $incomingStatus = strtolower($payload['status'] ?? $payload['payment'] ?? $payment->status ?? '');

        if ($incomingStatus) {
            $payment->status = $incomingStatus;
            $payment->save();
        }

        $order = $payment->order;
        if (! $order) {
            Log::error('Payment callback: Payment found but order missing', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
            ]);

            return null;
        }

        $statusService = app(StatusService::class);
        $cartService = app(CartService::class);
        $auditLogger = app(AuditLogger::class);

        if (in_array($incomingStatus, ['paid', 'success'])) {
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);
                $statusService->consumeInventory($order);

                // Set order status to "completed" after successful payment
                $completedStatus = OrderStatus::where('code', 'completed')->first();
                if ($completedStatus && $order->order_status_id !== $completedStatus->id) {
                    try {
                        $statusService->changeStatus($order, $completedStatus, null, __('order::messages.payment_processed'));
                    } catch (\DomainException $e) {
                        // If status transition is not allowed, log but don't fail
                        Log::warning('Could not change order status to completed after payment', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Clear cart for this user/guest after successful payment
                $cart = \Modules\Cart\Models\Cart::query()
                    ->active()
                    ->where(function ($q) use ($order) {
                        $q->where('user_id', $order->user_id);
                        if ($order->guest_uuid) {
                            $q->orWhere('guest_uuid', $order->guest_uuid);
                        }
                    })
                    ->first();

                if ($cart) {
                    $cartService->finalizeCart($cart);
                }

                // Important notification / audit
                $auditLogger->log(
                    $order->user_id,
                    'orders.payment_success',
                    __('order::messages.payment_processed'),
                    [
                        'context' => 'orders',
                        'notification_type' => 'important',
                        'notification_message_key' => 'order::messages.payment_processed',
                        'notification_message_params' => ['order_id' => $order->id],
                        'title_key' => 'order::messages.payment_processed',
                        'title_params' => ['order_id' => $order->id],
                    ]
                );
            }
        } elseif (in_array($incomingStatus, ['canceled', 'cancelled', 'failed', 'error'])) {
            if ($order->payment_status !== 'failed') {
                $order->update([
                    'payment_status' => 'failed',
                    'canceled_at' => now(),
                ]);
                $statusService->releaseInventory($order);
            }
        }

        return $order;
    }

    protected function log(Payment $payment, string $direction, mixed $payload, ?int $statusCode = null): void
    {
        PaymentLog::create([
            'payment_id' => $payment->id,
            'direction' => $direction,
            'status_code' => $statusCode,
            'payload' => $payload,
        ]);
    }

    protected function buildRefNumber(int $orderId): string
    {
        return 'ORD-'.$orderId.'-'.now()->timestamp;
    }
}
