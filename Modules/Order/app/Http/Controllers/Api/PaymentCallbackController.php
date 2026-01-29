<?php

namespace Modules\Order\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Modules\Order\Services\PaymentService;
use Modules\Post\Services\PostPaymentService;

class PaymentCallbackController extends Controller
{
    use ApiResponse;

    public function webhook(Request $request, PaymentService $payments): JsonResponse
    {
        try {
            // First try to handle as Order payment
            $order = $payments->handleCallback($request->all());

            if ($order) {
                return $this->successResponse(
                    data: ['order_id' => $order->id],
                    message: __('order::messages.callback_processed')
                );
            }

            // If no Order payment found, try Post payment
            $postPaymentService = app(PostPaymentService::class);
            $post = $postPaymentService->handleCallback($request->all());

            if ($post) {
                return $this->successResponse(
                    data: ['post_id' => $post->id, 'is_paid' => $post->is_paid],
                    message: __('post::post.messages.payment_success')
                );
            }

            return $this->errorResponse(
                message: __('order::messages.callback_ignored'),
                errors: null,
                status: 200
            );
        } catch (\Exception $e) {
            Log::error('Payment webhook callback error', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                message: __('order::messages.callback_failed'),
                errors: null,
                status: 500
            );
        }
    }

    public function result(Request $request, PaymentService $payments, ?string $invoiceId = null, ?string $status = null): RedirectResponse
    {
        // Log all incoming data for debugging
        Log::info('Payment result callback received', [
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'route_params' => [
                'invoiceId' => $invoiceId,
                'status' => $status,
            ],
            'query' => $request->query(),
            'all' => $request->all(),
        ]);

        try {
            // Sadad redirects with invoice ID and status in the URL path: /pay/{invoiceId}/Success or /pay/{invoiceId}/Failed
            // Extract invoice ID from route param, query params, or path
            $invoiceId = $invoiceId
                ?? $request->input('invoice_id')
                ?? $request->input('InvoiceId')
                ?? $request->input('invoiceId')
                ?? null;

            // Extract status from route param or URL path (e.g., /pay/4318772160980/Success)
            $statusFromPath = $status ? strtolower($status) : null;

            if (! $statusFromPath) {
                $pathSegments = explode('/', trim($request->path(), '/'));
                // Check if last segment is Success/Failed/Cancel
                if (! empty($pathSegments)) {
                    $lastSegment = strtolower(end($pathSegments));
                    if (in_array($lastSegment, ['success', 'failed', 'cancel', 'cancelled'])) {
                        $statusFromPath = $lastSegment;
                    }
                    // If second-to-last segment looks like an invoice ID, use it
                    if (count($pathSegments) >= 2 && is_numeric($pathSegments[count($pathSegments) - 2])) {
                        $invoiceId = $invoiceId ?? $pathSegments[count($pathSegments) - 2];
                    }
                }
            }

            // Build payload for callback handler
            $payload = array_merge($request->all(), [
                'invoice_id' => $invoiceId,
                'InvoiceId' => $invoiceId,
                'invoiceId' => $invoiceId,
                'status' => $statusFromPath ?? $request->input('status', $request->input('payment', '')),
                'payment' => $statusFromPath ?? $request->input('payment', ''),
            ]);

            Log::info('Payment callback payload prepared', ['payload' => $payload]);

            // First try to handle as Order payment
            $order = $payments->handleCallback($payload);
            
            // If no Order payment found, try Post payment
            $post = null;
            if (! $order) {
                $postPaymentService = app(PostPaymentService::class);
                $post = $postPaymentService->handleCallback($payload);
                
                if ($post) {
                    Log::info('Post payment callback processed', [
                        'post_id' => $post->id,
                        'is_paid' => $post->is_paid,
                    ]);
                }
            }

            $paymentFlag = strtolower($statusFromPath ?? $request->input('payment', ''));

            // Check if payment was successful (either Order or Post)
            $isSuccess = ($order && in_array($order->payment_status, ['paid', 'Paid']))
                || ($post && $post->is_paid)
                || $paymentFlag === 'success';

            if ($isSuccess) {
                Log::info('Payment successful, redirecting to success page', [
                    'order_id' => $order?->id,
                    'post_id' => $post?->id,
                    'payment_status' => $order?->payment_status ?? ($post?->is_paid ? 'paid' : 'pending'),
                ]);

                if (Route::has('front.checkout.payment-success')) {
                    return redirect()->route('front.checkout.payment-success', $request->query());
                }

                return redirect()->away(config('app.url').'/');
            }

            Log::info('Payment failed or cancelled, redirecting to failure page', [
                'order_id' => $order?->id,
                'post_id' => $post?->id,
                'payment_status' => $order?->payment_status,
                'payment_flag' => $paymentFlag,
            ]);

            if (Route::has('front.checkout.payment-failed') || Route::has('front.checkout.payment-failure')) {
                $route = Route::has('front.checkout.payment-failed') ? 'front.checkout.payment-failed' : 'front.checkout.payment-failure';

                return redirect()->route($route, $request->query());
            }

            return redirect()->away(config('app.url').'/');
        } catch (\Exception $e) {
            Log::error('Payment result callback error', [
                'error' => $e->getMessage(),
                'url' => $request->fullUrl(),
                'path' => $request->path(),
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->away(config('app.url').'/');
        }
    }
}

