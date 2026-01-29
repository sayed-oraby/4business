<?php

namespace Modules\Authentication\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Modules\Activity\Services\AuditLogger;
use Modules\Authentication\Http\Requests\FrontendForgotPasswordRequest;
use Modules\Authentication\Http\Requests\FrontendResetPasswordRequest;
use Modules\Authentication\Http\Requests\FrontendVerifyOtpRequest;
use Modules\Authentication\Services\FrontendPasswordResetOtpService;
use Modules\User\Models\User;

class AuthController extends Controller
{
    public function __construct(
        protected FrontendPasswordResetOtpService $otpService,
        protected AuditLogger $audit
    ) {}

    public function showLogin()
    {
        return view('authentication::frontend.login');
    }

    public function login(Request $request)
    {
        if ($request->ajax()) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'phone' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Format mobile number
            $mobile = preg_replace('/[^0-9]/', '', $request->phone);

            if (Auth::guard('admin')->attempt(['mobile' => $mobile, 'password' => $request->password], $request->boolean('remember'))) {
                $user = Auth::guard('admin')->user();

                if (! $user->is_verified) {
                    $otp_code = rand(1000, 9999);
                    $user->update(['otp_code' => $otp_code]);
                    session()->flash('dev_otp', $otp_code);
                    session(['mobile' => $user->mobile]);

                    Auth::guard('admin')->logout();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'يرجى تفعيل الحساب أولاً',
                        'redirect' => route('frontend.otp'),
                    ]);
                }

                $request->session()->regenerate();

                return response()->json([
                    'status' => 'success',
                    'message' => __('authentication::messages.api.logged_in') ?? 'تم تسجيل الدخول بنجاح',
                    'redirect' => redirect()->intended(route('frontend.home'))->getTargetUrl(),
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => __('authentication::messages.invalid_credentials'),
                'errors' => [
                    'phone' => [__('authentication::messages.invalid_credentials')],
                ],
            ], 422);
        }

        $credentials = $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        // Format mobile number
        $mobile = preg_replace('/[^0-9]/', '', $credentials['phone']);

        if (Auth::guard('admin')->attempt(['mobile' => $mobile, 'password' => $credentials['password']], $request->boolean('remember'))) {
            $user = Auth::guard('admin')->user();

            if (! $user->is_verified) {
                $otp_code = rand(1000, 9999);
                $user->update(['otp_code' => $otp_code]);
                session()->flash('dev_otp', $otp_code);
                session(['mobile' => $user->mobile]);

                Auth::guard('admin')->logout();

                return redirect()->route('frontend.otp')->with('status', 'يرجى تفعيل الحساب أولاً');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('frontend.home'));
        }

        return back()->withErrors([
            'phone' => __('authentication::messages.invalid_credentials'),
        ])->onlyInput('phone');
    }

    public function showRegister()
    {
        return view('authentication::frontend.register');
    }

    public function register(Request $request)
    {
        if ($request->ajax()) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'account_type' => 'required|in:individual,office',
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20|unique:users,mobile',
                'password' => 'required|string|min:6|confirmed',
                'email' => 'nullable|email|unique:users,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422);
            }

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
            session()->flash('dev_otp', $otp_code);

            // Store mobile in session for OTP
            session(['mobile' => $mobile]);

            return response()->json([
                'status' => 'success',
                'message' => __('authentication::messages.registered'),
                'redirect' => route('frontend.otp'),
            ]);
        }

        $validated = $request->validate([
            'account_type' => 'required|in:individual,office',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,mobile',
            'password' => 'required|string|min:6|confirmed',
            'email' => 'nullable|email|unique:users,email',
        ]);

        $mobile = preg_replace('/[^0-9]/', '', $validated['phone']);

        $user = User::create([
            'account_type' => $validated['account_type'],
            'name' => $validated['name'],
            'email' => isset($validated['email']) ? $validated['email'] : null,
            'mobile' => $mobile,
            'password' => $validated['password'],
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

        session(['mobile' => $mobile]);

        $otp_code = rand(1000, 9999);
        $user->update(['otp_code' => $otp_code]);
        session()->flash('dev_otp', $otp_code);

        return redirect()->route('frontend.otp')->with('success', __('authentication::messages.registered'));
    }

    public function showOtp()
    {
        if (! session('mobile')) {
            return redirect()->route('frontend.login');
        }

        return view('authentication::frontend.otp');
    }

    public function verifyOtp(Request $request)
    {
        if ($request->ajax()) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'otp' => 'required|array|size:4',
                'otp.*' => 'required|digits:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $otp = implode('', $request->otp);

            if (strlen($otp) !== 4) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('authentication::messages.invalid_otp'),
                    'errors' => ['otp' => [__('authentication::messages.invalid_otp')]],
                ], 422);
            }

            $mobile = session('mobile');
            $user = User::where('mobile', $mobile)->first();

            if ($user && $user->otp_code == $otp) {
                $user->update([
                    'is_verified' => true,
                    'otp_code' => null, // Clear OTP after verification
                    'email_verified_at' => now(),
                ]);

                Auth::guard('admin')->login($user);
                session()->forget('mobile');

                return response()->json([
                    'status' => 'success',
                    'message' => __('authentication::messages.verified'),
                    'redirect' => route('frontend.home'),
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => __('authentication::messages.invalid_otp'),
            ], 422);
        }

        $request->validate([
            'otp' => 'required|array|size:4',
            'otp.*' => 'required|digits:1',
        ]);

        $otp = implode('', $request->otp);

        // TODO: Verify OTP from cache/database
        // For now, accept any 4-digit code
        if (strlen($otp) !== 4) {
            return back()->withErrors(['otp' => __('authentication::messages.invalid_otp')]);
        }

        $mobile = session('mobile');
        $user = User::where('mobile', $mobile)->first();

        if ($user && $user->otp_code == $otp) {
            $user->update([
                'is_verified' => true,
                'otp_code' => null,
                'email_verified_at' => now(),
            ]);

            Auth::guard('admin')->login($user);
            session()->forget('mobile');
        } else {
            return back()->withErrors(['otp' => __('authentication::messages.invalid_otp')]);
        }

        return redirect()->route('frontend.home')->with('success', __('authentication::messages.verified'));
    }

    public function resendOtp(Request $request)
    {
        $mobile = session('mobile');

        if (! $mobile) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Session expired',
                    'redirect' => route('frontend.login'),
                ], 401);
            }

            return redirect()->route('frontend.login');
        }

        // TODO: Send new OTP
        $user = User::where('mobile', $mobile)->first();
        if ($user) {
            $otp_code = rand(1000, 9999);
            $user->update(['otp_code' => $otp_code]);
            session()->flash('dev_otp', $otp_code);
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم الأرسال بنجاح',
            ]);
        }

        return back()->with('status', 'تم الأرسال بنجاح');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('frontend.home');
    }

    public function socialRedirect(string $provider)
    {
        // TODO: Implement social login with Laravel Socialite
        return redirect()->route('frontend.login')->with('error', __('authentication::messages.social_not_available'));
    }

    public function socialCallback(string $provider)
    {
        // TODO: Implement social login callback
        return redirect()->route('frontend.login');
    }

    public function showPasswordRequest()
    {
        return view('authentication::frontend.forgot-password');
    }

    public function sendPasswordResetOtp(FrontendForgotPasswordRequest $request)
    {
        $phone = $request->validated('phone');
        $mobile = preg_replace('/[^0-9]/', '', $phone);

        $otp = $this->otpService->create($mobile);

        // TODO: Send OTP via SMS service
        // For development, we'll just flash it to session
        // In production, integrate with SMS service like Twilio, Nexmo, etc.

        // For testing purposes, show OTP in session
        session()->flash('dev_otp', $otp);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => __('authentication::messages.password.otp_sent_mobile'),
                'redirect' => route('frontend.password.otp', ['mobile' => base64_encode($mobile)]),
            ]);
        }

        return redirect()
            ->route('frontend.password.otp', ['mobile' => base64_encode($mobile)])
            ->with('status', __('authentication::messages.password.otp_sent_mobile'));
    }

    public function showPasswordOtp(Request $request)
    {
        $mobile = base64_decode($request->query('mobile', ''));

        if (! $mobile) {
            return redirect()->route('frontend.password.request');
        }

        return view('authentication::frontend.verify-password-otp', [
            'mobile' => $mobile,
        ]);
    }

    public function verifyPasswordOtp(FrontendVerifyOtpRequest $request)
    {
        $data = $request->validated();

        if (! $this->otpService->validate($data['mobile'], $data['otp'])) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('authentication::messages.password.invalid_otp'),
                    'errors' => ['otp' => [__('authentication::messages.password.invalid_otp')]],
                ], 422);
            }

            return back()
                ->withErrors(['otp' => __('authentication::messages.password.invalid_otp')])
                ->withInput();
        }

        Session::put('password_reset', [
            'mobile' => $data['mobile'],
            'otp' => $data['otp'],
            'verified_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => __('authentication::messages.password.otp_verified'),
                'redirect' => route('frontend.password.reset'),
            ]);
        }

        return redirect()->route('frontend.password.reset')
            ->with('status', __('authentication::messages.password.otp_verified'));
    }

    public function resendPasswordOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
        ]);

        $mobile = $request->input('mobile');

        // Check if user exists
        $user = User::where('mobile', $mobile)->first();
        if (! $user) {
            return back()->withErrors(['mobile' => __('authentication::messages.phone_not_found')]);
        }

        // Generate new OTP
        $otp = $this->otpService->create($mobile);

        info('dev_otp');
        info($otp);

        // TODO: Send OTP via SMS service
        // For testing purposes, show OTP in session
        session()->flash('dev_otp', $otp);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => __('authentication::messages.password.otp_resent'),
            ]);
        }

        return back()->with('status', __('authentication::messages.password.otp_resent'));
    }

    public function showPasswordReset()
    {
        $session = Session::get('password_reset');

        if (! $session) {
            return redirect()->route('frontend.password.request');
        }

        return view('authentication::frontend.reset-password', [
            'mobile' => $session['mobile'],
        ]);
    }

    public function resetPassword(FrontendResetPasswordRequest $request)
    {
        $session = Session::get('password_reset');

        if (! $session || $session['mobile'] !== $request->validated('mobile')) {
            return redirect()->route('frontend.password.request');
        }

        if (! $this->otpService->validate($session['mobile'], $session['otp'])) {
            return redirect()->route('frontend.password.request')
                ->withErrors(['mobile' => __('authentication::messages.password.invalid_otp')]);
        }

        $user = User::where('mobile', $session['mobile'])->firstOrFail();
        $user->forceFill([
            'password' => Hash::make($request->validated('password')),
        ])->save();

        $this->otpService->delete($session['mobile']);
        Session::forget('password_reset');

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => __('authentication::messages.password.reset_success'),
                'redirect' => route('frontend.login'),
            ]);
        }

        return redirect()
            ->route('frontend.login')
            ->with('success', __('authentication::messages.password.reset_success'));
    }
}
