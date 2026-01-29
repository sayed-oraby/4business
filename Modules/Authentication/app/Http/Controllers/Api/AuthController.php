<?php

namespace Modules\Authentication\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Activity\Services\AuditLogger;
use Modules\Authentication\Http\Requests\LoginRequest;
use Modules\Authentication\Http\Requests\PhoneVerifyRequest;
use Modules\Authentication\Http\Requests\RegisterRequest;
use Modules\Authentication\Services\FrontendPasswordResetOtpService;
use Modules\Cart\Models\Wishlist;
use Modules\Cart\Services\WishlistService;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Models\User;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected FrontendPasswordResetOtpService $resetOtpService,
        protected AuditLogger $audit,
        protected WishlistService $wishlistService
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $mobile = preg_replace('/[^0-9]/', '', $request->phone);

        $user = User::create([
            'account_type' => $request->account_type,
            'name' => $request->name,
            'email' => $request->email ?: null,
            'mobile' => $mobile,
            'password' => $request->password,
        ]);

        $titleEn = "User {$user->name} registered as {$user->account_type}";
        $titleAr = "تم تسجيل المستخدم {$user->name} كـ {$user->account_type}";

        $this->audit->log(
            $user->id,
            'users.registered',
            $titleEn,
            [
                'context' => 'users',
                'level' => 'success',
                'notification_type' => 'alert',
                'notification_message' => $titleEn,
                'title_translations' => ['en' => $titleEn, 'ar' => $titleAr],
                'message_translations' => ['en' => $titleEn, 'ar' => $titleAr],
                'user_id' => $user->id,
                'account_type' => $user->account_type,
            ]
        );

        $otp_code = rand(1000, 9999);
        $user->update(['otp_code' => $otp_code]);

        return response()->json([
            'status' => 'success',
            'message' => __('authentication::messages.registered'),
            'dev_otp' => config('app.debug') ? $otp_code : null
        ]);
    }


    public function verifyAccount(PhoneVerifyRequest $request): JsonResponse
    {
        $mobile = preg_replace('/[^0-9]/', '', $request->phone);
        $otp = $request->otp;

        $user = User::where('mobile', $mobile)->first();

        if ($user && $user->otp_code == $otp) {
            $user->update([
                'is_verified' => true,
                'otp_code' => null,
                'email_verified_at' => now(),
            ]);

            return $this->issueTokenResponse($user, $request);
        }

        return response()->json([
            'status' => 'error',
            'message' => __('authentication::messages.invalid_otp')
        ], 422);
    }


    public function resendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $mobile = preg_replace('/[^0-9]/', '', $request->phone);
        $user = User::where('mobile', $mobile)->first();

        if ($user) {
            // Check if user is already verified? Frontend doesn't check
            $otp_code = rand(1000, 9999);
            $user->update(['otp_code' => $otp_code]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم الأرسال بنجاح',
                'dev_otp' => config('app.debug') ? $otp_code : null
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => __('authentication::messages.user_not_found')
        ], 404);
    }



    public function login(LoginRequest $request): JsonResponse
    {
        $mobile = preg_replace('/[^0-9]/', '', $request->phone);
        $user = User::where('mobile', $mobile)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => __('authentication::messages.invalid_credentials'),
            ], 401);
        }

        if (! $user->is_verified) {
            $otp_code = rand(1000, 9999);
            $user->update(['otp_code' => $otp_code]);
            
            return response()->json([
                'status' => 'error',
                'code' => 'not_verified',
                'message' => 'يرجى تفعيل الحساب أولاً',
                'dev_otp' => config('app.debug') ? $otp_code : null
            ], 403);
        }

        return $this->issueTokenResponse($user, $request);
    }





    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $mobile = preg_replace('/[^0-9]/', '', $request->phone);
        
        // Ensure user exists
        $user = User::where('mobile', $mobile)->first();
        if (!$user) {
             // Return success to avoid enumeration? Frontend returns redirect with status even if not found? 
             // Frontend: creates OTP in DB table via service. Service uses updateOrInsert. So it doesn't strictly require User existence in Users table?
             // But wait, FrontendPasswordResetOtpService checks:
             // DB::table('password_reset_tokens')...
             // Frontend: Controller doesn't check user existence before sending OTP. BUT ResetPassword checks User existence.
             // It is better to check if user exists to avoid sending OTP to non-users.
             // However, to match frontend behavior exactly... Frontend sends OTP.
             // Wait, Frontend resendPasswordOtp CHECKS if user exists. sendPasswordResetOtp DOES NOT.
             // I will implement check anyway for API cleanliness, or just call service.
             // BUT, validation of phone number is good.
        }

        $otp = $this->resetOtpService->create($mobile);

        return response()->json([
            'status' => 'success',
            'message' => __('authentication::messages.password.otp_sent_mobile'),
            'dev_otp' => config('app.debug') ? $otp : null
        ]);
    }

    
    public function verifyPasswordOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $mobile = preg_replace('/[^0-9]/', '', $request->phone);

        if (! $this->resetOtpService->validate($mobile, $request->otp)) {
             return response()->json([
                'status' => 'error',
                'message' => __('authentication::messages.password.invalid_otp'),
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => __('authentication::messages.password.otp_verified'),
        ]);
    }


    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $mobile = preg_replace('/[^0-9]/', '', $request->phone);

        // Verify OTP again (Stateless API)
        if (! $this->resetOtpService->validate($mobile, $request->otp)) {
             return response()->json([
                'status' => 'error',
                'message' => __('authentication::messages.password.invalid_otp'),
            ], 422);
        }

        $user = User::where('mobile', $mobile)->firstOrFail();

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        $this->resetOtpService->delete($mobile);
        $user->tokens()->delete(); // Logout all devices

        return response()->json([
            'status' => 'success',
            'message' => __('authentication::messages.password.reset_success'),
        ]);
    }





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
            expiresAt: null, // Sanctum default
            message: __('authentication::messages.api.logged_in')
        );
    }

    protected function attemptWishlistMerge(?string $guestUuid, User $user): void
    {
        if (! $guestUuid) {
            return;
        }

        try {
            $guestWishlist = Wishlist::where('guest_uuid', $guestUuid)->first();
            if (! $guestWishlist) {
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
