<?php

namespace Modules\Post\Services;

use Illuminate\Support\Facades\Log;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostPayment;
use Modules\Post\Models\PostPaymentLog;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;
use Exception;

class PostPaymentService
{
    /**
     * @var array
     */
    protected $mfConfig = [];

    /**
     * Initiate MyFatoorah Configuration
     */
    public function __construct()
    {
        $this->mfConfig = [
            'apiKey'      => config('myfatoorah.api_key'),
            'isTest'      => config('myfatoorah.test_mode'),
            'countryCode' => config('myfatoorah.country_iso'),
        ];
    }

    /**
     * Create a MyFatoorah payment for a post package.
     */
    public function createMyFatoorahPayment(Post $post, array $customer, string $callbackUrl): PostPayment
    {
        $ref = $this->buildRefNumber($post->id);
        $amount = $post->package->price ?? 0;

        $payment = PostPayment::create([
            'post_id' => $post->id,
            'ref_number' => $ref,
            'provider' => 'myfatoorah',
            'amount' => $amount,
            'currency' => 'KWD',
            'status' => 'pending',
            'callback_url' => $callbackUrl,
        ]);

        $this->log($payment, 'request', [
            'post_id' => $post->id,
            'amount' => $amount,
            'customer' => $customer,
        ]);

        try {
            $curlData = $this->getPayLoadData($post, $customer, $callbackUrl, $ref);

            $mfObj = new MyFatoorahPayment($this->mfConfig);
            $paymentData = $mfObj->getInvoiceURL($curlData, 0, $ref);

            $this->log($payment, 'response', $paymentData);

            if (!isset($paymentData['invoiceURL'])) {
                $payment->update(['status' => 'failed']);
                Log::error('MyFatoorah invoice creation failed for post payment: Invoice URL missing', [
                    'payment_id' => $payment->id,
                    'post_id' => $post->id,
                    'response' => $paymentData,
                ]);

                throw new \RuntimeException(__('post::post.messages.invoice_url_missing'));
            }

            $payment->update([
                'invoice_url' => $paymentData['invoiceURL'],
                'payload' => $paymentData,
            ]);

            return $payment;
        } catch (Exception $e) {
            $payment->update(['status' => 'failed']);
            $this->log($payment, 'error', ['exception' => $e->getMessage()], 500);
            Log::error('MyFatoorah payment creation failed for post', [
                'payment_id' => $payment->id,
                'post_id' => $post->id,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException(__('post::post.messages.payment_creation_failed'));
        }
    }

    /**
     * Handle payment callback from MyFatoorah.
     */
    public function handleCallback(array $payload): ?Post
    {
        $paymentId = $payload['paymentId'] ?? null;
        $customerReference = $payload['Id'] ?? null;

        if (!$paymentId && !$customerReference) {
            Log::warning('Post payment callback received without paymentId', ['payload' => $payload]);
            return null;
        }

        try {
            $mfObj = new MyFatoorahPaymentStatus($this->mfConfig);
            $data = $mfObj->getPaymentStatus($paymentId, 'PaymentId');

            $ref = $data->CustomerReference ?? null;

            if (!$ref) {
                Log::warning('MyFatoorah callback: CustomerReference not found', ['payload' => $payload]);
                return null;
            }

            $payment = PostPayment::where('ref_number', $ref)->first();

            if (!$payment) {
                Log::debug('Post payment not found, this callback may belong to another module', [
                    'ref' => $ref,
                    'payment_id' => $paymentId,
                ]);
                return null;
            }

            $this->log($payment, 'webhook', [
                'payment_data' => $data,
                'payload' => $payload
            ], 200);

            $invoiceStatus = strtolower($data->InvoiceStatus ?? '');

            // Update payment status based on MyFatoorah response
            if ($invoiceStatus === 'paid') {
                $payment->status = 'paid';
            } elseif (in_array($invoiceStatus, ['failed', 'cancelled', 'expired'])) {
                $payment->status = $invoiceStatus;
            }

            $payment->save();

            $post = $payment->post;
            if (!$post) {
                Log::error('Post payment callback: Payment found but post missing', [
                    'payment_id' => $payment->id,
                    'post_id' => $payment->post_id,
                ]);
                return null;
            }

            if ($invoiceStatus === 'paid') {
                if (!$post->is_paid) {
                    $post->update([
                        'is_paid' => true,
                        'status' => 'pending', // Ready for admin review
                    ]);

                    Log::info('Post payment successful via MyFatoorah', [
                        'post_id' => $post->id,
                        'payment_id' => $payment->id,
                    ]);

                    // Trigger notifications
                    try {
                        $postService = app(PostService::class);
                        $postService->notifyNewPost($post, $post->user);
                        $postService->notifyPaymentSuccess($post);
                    } catch (Exception $e) {
                        Log::error('Failed to send post payment notifications', [
                            'post_id' => $post->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            } elseif (in_array($invoiceStatus, ['cancelled', 'failed', 'expired'])) {
                $post->update([
                    'is_paid' => false,
                    'status' => 'payment_failed',
                ]);
            }

            return $post;
        } catch (Exception $e) {
            Log::error('MyFatoorah callback processing failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
            return null;
        }
    }

    /**
     * Map post data to MyFatoorah payload
     */
    protected function getPayLoadData(Post $post, array $customer, string $callbackUrl, string $ref): array
    {
        return [
            'CustomerName'       => $customer['name'] ?? 'Guest',
            'InvoiceValue'       => $post->package->price ?? 0,
            'DisplayCurrencyIso' => 'KWD',
            'CustomerEmail'      => $customer['email'] ?? '',
            'CallBackUrl'        => $callbackUrl,
            'ErrorUrl'           => $callbackUrl,
            'MobileCountryCode'  => '+965',
            'CustomerMobile'     => $this->formatMobileNumber($customer['mobile'] ?? ''),
            'Language'           => app()->getLocale() === 'ar' ? 'ar' : 'en',
            'CustomerReference'  => $ref,
            'SourceInfo'         => 'Laravel ' . app()::VERSION . ' - MyFatoorah Package ' . MYFATOORAH_LARAVEL_PACKAGE_VERSION
        ];
    }

    /**
     * Format mobile number for MyFatoorah (max 11 characters, no country code)
     */
    protected function formatMobileNumber(string $mobile): string
    {
        // Remove all non-numeric characters
        $mobile = preg_replace('/[^0-9]/', '', $mobile);

        // Remove country code if present (965 for Kuwait)
        $mobile = preg_replace('/^965/', '', $mobile);

        // Ensure maximum 11 characters
        return substr($mobile, 0, 11);
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
