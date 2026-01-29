<?php

namespace Modules\Post\Services;

use Illuminate\Support\Facades\Log;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostPayment;
use Modules\Post\Models\PostPaymentLog;
use Sadad\Library\SadadLibrary;

class PostPaymentServiceold
{
    /**
     * Create a Sadad payment for a post package.
     */
    public function createSadadPayment(Post $post, array $customer, string $callbackUrl): PostPayment
    {
        $ref = $this->buildRefNumber($post->id);
        $amount = $post->package->price ?? 0;

        $payment = PostPayment::create([
            'post_id' => $post->id,
            'ref_number' => $ref,
            'provider' => 'sadad',
            'amount' => $amount,
            'currency' => 'KWD',
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
            'amount' => SadadLibrary::getKWDAmount('KWD', $amount),
            'customer_Name' => $customer['name'] ?? '',
            'customer_Mobile' => SadadLibrary::validatePhone($customer['mobile'] ?? ''),
            'customer_Email' => $customer['email'] ?? '',
            'currency_Code' => 'KWD',
            'callback_url' => $callbackUrl,
        ];

        $requestArray = ['Invoices' => [$invoice]];

        $this->log($payment, 'request', $requestArray);

        try {
            $sadadInvoice = $sadad->createInvoice($requestArray, $sadad->refreshToken);

            $this->log($payment, 'response', $sadadInvoice);

            if (! isset($sadadInvoice['InvoiceURL'])) {
                $payment->update(['status' => 'failed']);
                Log::error('Sadad invoice creation failed for post payment: Invoice URL missing', [
                    'payment_id' => $payment->id,
                    'post_id' => $post->id,
                    'response' => $sadadInvoice,
                ]);

                throw new \RuntimeException(__('post::post.messages.invoice_url_missing'));
            }
        } catch (\Exception $e) {
            $payment->update(['status' => 'failed']);
            $this->log($payment, 'error', ['exception' => $e->getMessage()], 500);
            Log::error('Sadad payment creation failed for post', [
                'payment_id' => $payment->id,
                'post_id' => $post->id,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException(__('post::post.messages.payment_creation_failed'));
        }

        $payment->update([
            'invoice_url' => $sadadInvoice['InvoiceURL'],
            'payload' => $sadadInvoice,
        ]);

        return $payment;
    }

    /**
     * Handle payment callback from Sadad.
     */
    public function handleCallback(array $payload): ?Post
    {
        $ref = $payload['ref_Number'] ?? $payload['ref_number'] ?? null;
        $invoiceId = $payload['invoice_id'] ?? $payload['InvoiceId'] ?? $payload['invoiceId'] ?? null;

        if (! $ref && ! $invoiceId) {
            Log::warning('Post payment callback received without ref_number or invoice_id', ['payload' => $payload]);
            return null;
        }

        $payment = null;

        // First try to find by ref_number
        if ($ref) {
            $payment = PostPayment::where('ref_number', $ref)->first();
        }

        // If not found by ref, try to find by invoice_id in payload JSON
        if (! $payment && $invoiceId) {
            // Try multiple JSON paths where Sadad might store the invoice ID
            $payment = PostPayment::where('payload->InvoiceId', $invoiceId)
                ->orWhere('payload->invoice_id', $invoiceId)
                ->orWhere('payload->Invoices->0->InvoiceId', $invoiceId)
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(payload, '$.InvoiceId')) = ?", [$invoiceId])
                ->first();
        }

        if (! $payment) {
            Log::debug('Post payment not found, this callback may belong to Order payment', [
                'ref' => $ref,
                'invoice_id' => $invoiceId,
            ]);
            return null;
        }

        $this->log($payment, 'webhook', $payload, 200);

        $incomingStatus = strtolower($payload['status'] ?? $payload['payment'] ?? $payment->status ?? '');

        if ($incomingStatus) {
            $payment->status = $incomingStatus;
            $payment->save();
        }

        $post = $payment->post;
        if (! $post) {
            Log::error('Post payment callback: Payment found but post missing', [
                'payment_id' => $payment->id,
                'post_id' => $payment->post_id,
            ]);
            return null;
        }

        if (in_array($incomingStatus, ['paid', 'success'])) {
            if (! $post->is_paid) {
                $post->update([
                    'is_paid' => true,
                    'status' => 'pending', // Ready for admin review
                ]);

                Log::info('Post payment successful', [
                    'post_id' => $post->id,
                    'payment_id' => $payment->id,
                ]);

                // Trigger notifications
                try {
                    $postService = app(PostService::class);
                    $postService->notifyNewPost($post, $post->user);
                    $postService->notifyPaymentSuccess($post);
                } catch (\Exception $e) {
                    Log::error('Failed to send post payment notifications', [
                        'post_id' => $post->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } elseif (in_array($incomingStatus, ['canceled', 'cancelled', 'failed', 'error'])) {
            $post->update([
                'is_paid' => false,
                'status' => 'payment_failed',
            ]);
        }

        return $post;
    }

    /**
     * Log payment activity.
     */
    protected function log(PostPayment $payment, string $direction, mixed $payload, ?int $statusCode = null): void
    {
        PostPaymentLog::create([
            'post_payment_id' => $payment->id,
            'direction' => $direction,
            'status_code' => $statusCode,
            'payload' => $payload,
        ]);
    }

    /**
     * Build reference number for payment.
     */
    protected function buildRefNumber(int $postId): string
    {
        return 'POST-' . $postId . '-' . now()->timestamp;
    }
}
