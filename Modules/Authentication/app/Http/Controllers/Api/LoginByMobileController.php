<?php

namespace Modules\Authentication\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Activity\Services\AuditLogger;
use Modules\Cart\Models\Wishlist;
use Modules\Cart\Services\WishlistService;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Models\User;

class LoginByMobileController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected AuditLogger $audit,
        protected WishlistService $wishlistService
    ) {
    }

    /**
     * Step 1: Send OTP to mobile number
     * - If user exists: send OTP for login
     * - If user doesn't exist: create new user and send OTP
     */
    public function loginWithMobile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $mobile = preg_replace('/[^0-9]/', '', $request->phone);
        
        // Find or create user
        $user = User::where('mobile', $mobile)->first();

        $isNewUser = false;

        if (!$user) {
            // Create new user with mobile only
            $user = User::create([
                'mobile' => $mobile,
                'name' => 'مستخدم ' . substr($mobile, -4), // Temporary name
                'password' => bcrypt(Str::random(16)), // Random password
                'account_type' => 'client', // Default account type
                'is_verified' => false,
            ]);
            $isNewUser = true;

            // Log registration
            // $titleEn = "New user registered with mobile {$mobile}";
            // $titleAr = "تم تسجيل مستخدم جديد برقم الجوال {$mobile}";

            // $this->audit->log(
            //     $user->id,
            //     'users.registered',
            //     $titleEn,
            //     [
            //         'context' => 'users',
            //         'level' => 'success',
            //         'notification_type' => 'alert',
            //         'notification_message' => $titleEn,
            //         'title_translations' => ['en' => $titleEn, 'ar' => $titleAr],
            //         'message_translations' => ['en' => $titleEn, 'ar' => $titleAr],
            //         'user_id' => $user->id,
            //         'account_type' => $user->account_type,
            //     ]
            // );
        }

        // Generate and save OTP
        $otp_code = rand(1000, 9999);

        $user->update([
            'otp_code' => $otp_code,
            'otp_expires_at' => now()->addMinutes(5), // OTP valid for 5 minutes
        ]);

        // TODO: Send OTP via SMS service
        // SmsService::send($mobile, "رمز التحقق: {$otp_code}");

        return response()->json([
            'status' => 'success',
            'message' => __('authentication::messages.otp_sent'),
            'is_new_user' => $isNewUser,
            'dev_otp' => config('app.debug') ? $otp_code : null
        ]);
    }

    /**
     * Step 2: Verify OTP and login/complete registration
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp' => 'required|string|size:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $mobile = preg_replace('/[^0-9]/', '', $request->phone);
        $otp = $request->otp;

        $user = User::where('mobile', $mobile)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => __('authentication::messages.user_not_found')
            ], 404);
        }

        // Check if OTP is valid
        if ($user->otp_code != $otp) {
            return response()->json([
                'status' => 'error',
                'message' => __('authentication::messages.invalid_otp')
            ], 422);
        }

        // Check if OTP is expired (if otp_expires_at column exists)
        if ($user->otp_expires_at && now()->gt($user->otp_expires_at)) {
            return response()->json([
                'status' => 'error',
                'code' => 'otp_expired',
                'message' => __('authentication::messages.otp_expired')
            ], 422);
        }

        // OTP is valid - verify user and clear OTP
        $user->update([
            'is_verified' => true,
            'otp_code' => null,
            'otp_expires_at' => null,
            'email_verified_at' => now(),
        ]);

        return $this->issueTokenResponse($user, $request);
    }

    /**
     * Resend OTP to mobile number
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $mobile = preg_replace('/[^0-9]/', '', $request->phone);
        $user = User::where('mobile', $mobile)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => __('authentication::messages.user_not_found')
            ], 404);
        }

        // Generate new OTP
        $otp_code = rand(1000, 9999);
        $user->update([
            'otp_code' => $otp_code,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        // TODO: Send OTP via SMS service

        return response()->json([
            'status' => 'success',
            'message' => __('authentication::messages.otp_resent'),
            'dev_otp' => config('app.debug') ? $otp_code : null
        ]);
    }

    /**
     * Issue token response after successful authentication
     */
    protected function issueTokenResponse(User $user, Request $request): JsonResponse
    {
        $token = $user->createToken('auth-token')->plainTextToken;
        $guestUuid = $request->input('guest_uuid');

        if ($guestUuid) {
            $this->attemptWishlistMerge($guestUuid, $user);
        }

        return $this->authSuccessResponse(
            accessToken: $token,
            user: (new UserResource($user))->resolve(),
            tokenType: 'Bearer',
            expiresAt: null,
            message: __('authentication::messages.api.logged_in')
        );
    }

    /**
     * Merge guest wishlist with user wishlist
     */
    protected function attemptWishlistMerge(?string $guestUuid, User $user): void
    {
        if (!$guestUuid) {
            return;
        }

        try {
            $guestWishlist = Wishlist::where('guest_uuid', $guestUuid)->first();
            if (!$guestWishlist) {
                return;
            }

            $userWishlist = Wishlist::firstOrCreate(['user_id' => $user->id], ['guest_uuid' => null]);

            if ($guestWishlist->id === $userWishlist->id) {
                return;
            }

            $this->wishlistService->merge($guestWishlist, $userWishlist);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
