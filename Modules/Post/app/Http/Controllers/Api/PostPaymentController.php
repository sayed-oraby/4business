<?php

namespace Modules\Post\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Post\Models\Post;
use Modules\Post\Services\PostPaymentService;
use Modules\Post\Services\PostService;

class PostPaymentController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected PostPaymentService $paymentService,
        protected PostService $postService
    ) {
    }

    /**
     * Handle payment callback from MyFatoorah gateway.
     *
     * @api {any} /api/v1/posts/{post}/payment/callback Payment Callback
     * @apiName PostPaymentCallback
     * @apiGroup Post Payment
     * @apiDescription This endpoint is called by MyFatoorah after payment completion
     *
     * @apiParam {String} post Post UUID
     * @apiParam {String} paymentId Payment ID from MyFatoorah
     *
     * @apiSuccess {Boolean} success Success status
     * @apiSuccess {String} message Success message
     * @apiSuccess {Object} data Response data
     * @apiSuccess {String} data.post_id Post ID
     * @apiSuccess {String} data.status Payment status (paid/failed)
     *
     * @apiSuccessExample {json} Success Response:
     * {
     *   "success": true,
     *   "message": "Payment successful",
     *   "data": {
     *     "post_id": "123",
     *     "status": "paid"
     *   }
     * }
     */
    public function callback(Request $request, Post $post): JsonResponse
    {
        $payload = $request->all();

        try {
            $updatedPost = $this->paymentService->handleCallback($payload);

            if ($updatedPost && $updatedPost->is_paid) {
                // Payment successful - notify admins about new post
                $this->postService->notifyNewPost($updatedPost, $updatedPost->user);
                $this->postService->notifyPaymentSuccess($updatedPost);

                return $this->successResponse(
                    data: ['post_id' => $updatedPost->id, 'status' => 'paid'],
                    message: __('post::post.messages.payment_success')
                );
            }

            if ($updatedPost) {
                return $this->errorResponse(
                    message: __('post::post.messages.payment_failed'),
                    status: 400
                );
            }

            return $this->errorResponse(
                message: __('post::post.messages.payment_not_found'),
                status: 404
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: __('post::post.messages.payment_callback_error'),
                status: 500
            );
        }
    }

    /**
     * Retry payment for a post that failed or is awaiting payment.
     *
     * @api {post} /api/v1/posts/{post}/payment/retry Retry Payment
     * @apiName RetryPostPayment
     * @apiGroup Post Payment
     * @apiDescription Generate a new payment link for a post that requires payment
     * @apiPermission authenticated
     *
     * @apiParam {String} post Post UUID
     *
     * @apiHeader {String} Authorization Bearer token
     *
     * @apiSuccess {Boolean} success Success status
     * @apiSuccess {String} message Success message
     * @apiSuccess {Object} data Response data
     * @apiSuccess {Object} data.payment Payment information
     * @apiSuccess {Number} data.payment.amount Payment amount
     * @apiSuccess {String} data.payment.currency Currency code (KWD)
     * @apiSuccess {String} data.payment.invoice_url MyFatoorah payment URL
     * @apiSuccess {String} data.payment.ref_number Reference number
     *
     * @apiSuccessExample {json} Success Response:
     * {
     *   "success": true,
     *   "message": "Payment link generated",
     *   "data": {
     *     "payment": {
     *       "amount": 15.00,
     *       "currency": "KWD",
     *       "invoice_url": "https://demo.myfatoorah.com/invoice/xyz",
     *       "ref_number": "POST-123-1737890123"
     *     }
     *   }
     * }
     *
     * @apiError (403) Unauthorized You don't own this post
     * @apiError (400) AlreadyPaid Post is already paid
     * @apiError (400) FreePackage Post has a free package
     *
     * @apiErrorExample {json} Error Response:
     * {
     *   "success": false,
     *   "message": "Unauthorized",
     *   "status": 403
     * }
     */
    public function retry(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();

        // Verify ownership
        if ($post->user_id !== $user->id) {
            return $this->errorResponse(
                message: __('post::post.messages.unauthorized'),
                status: 403
            );
        }

        // Check if post actually needs payment
        if ($post->is_paid) {
            return $this->errorResponse(
                message: __('post::post.messages.already_paid'),
                status: 400
            );
        }

        $package = $post->package;
        if ((float) $package->price <= 0) {
            return $this->errorResponse(
                message: __('post::post.messages.free_package'),
                status: 400
            );
        }

        try {
            $callbackUrl = route('api.posts.payment.callback', ['post' => $post->uuid]);

            $payment = $this->paymentService->createMyFatoorahPayment($post, [
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
            ], $callbackUrl);

            return $this->successResponse(
                data: [
                    'payment' => [
                        'amount' => $package->price,
                        'currency' => 'KWD',
                        'invoice_url' => $payment->invoice_url,
                        'ref_number' => $payment->ref_number,
                    ],
                ],
                message: __('post::post.messages.payment_link_generated')
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: __('post::post.messages.payment_creation_failed'),
                status: 500
            );
        }
    }
}
