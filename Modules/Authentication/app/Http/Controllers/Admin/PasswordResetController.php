<?php

namespace Modules\Authentication\Http\Controllers\Admin;

use Modules\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Modules\Activity\Jobs\RecordAuditLogJob;
use Modules\Authentication\Http\Requests\ForgotPasswordRequest;
use Modules\Authentication\Http\Requests\ResetPasswordRequest;
use Modules\Authentication\Http\Requests\VerifyOtpRequest;
use Modules\Authentication\Mail\AdminPasswordResetOtpMail;
use Modules\Authentication\Mail\AdminPasswordResetSuccessMail;
use Modules\Authentication\Services\PasswordResetOtpService;

class PasswordResetController extends Controller
{
    public function __construct(
        protected PasswordResetOtpService $otpService
    ) {
    }

    public function showLinkRequestForm()
    {
        return view('authentication::dashboard.auth.forgot-password');
    }

    public function sendOtp(ForgotPasswordRequest $request)
    {
        $email = $request->validated('email');
        $otp = $this->otpService->create($email);

        Mail::to($email)->queue(new AdminPasswordResetOtpMail($otp, $email, app()->getLocale()));

        $userId = User::query()->where('email', $email)->value('id');
        RecordAuditLogJob::dispatch(
            $userId,
            'auth.password.forgot',
            __('dashboard.actions.password_forgot'),
            [
                'context' => 'authentication',
                'email' => $email,
            ],
            'admin',
            $request->ip(),
            $request->userAgent()
        );

        return redirect()
            ->route('dashboard.password.otp', ['email' => $email])
            ->with('status', __('authentication::messages.password.otp_sent', ['email' => $email]));
    }

    public function showOtpForm(Request $request)
    {
        return view('authentication::dashboard.auth.verify-otp', [
            'email' => $request->query('email', old('email')),
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $data = $request->validated();

        if (! $this->otpService->validate($data['email'], $data['otp'])) {
            return back()
                ->withErrors(['otp' => __('authentication::messages.password.invalid_otp')])
                ->withInput();
        }

        Session::put('password_reset', [
            'email' => $data['email'],
            'otp' => $data['otp'],
            'verified_at' => now(),
        ]);

        $userId = User::query()->where('email', $data['email'])->value('id');
        RecordAuditLogJob::dispatch(
            $userId,
            'auth.password.otp_verified',
            __('dashboard.actions.password_otp_verified'),
            [
                'context' => 'authentication',
                'email' => $data['email'],
            ],
            'admin',
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('dashboard.password.reset')
            ->with('status', __('authentication::messages.password.otp_verified'));
    }

    public function showResetForm()
    {
        $session = Session::get('password_reset');

        if (! $session) {
            return redirect()->route('dashboard.password.request');
        }

        return view('authentication::dashboard.auth.reset-password', [
            'email' => $session['email'],
        ]);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $session = Session::get('password_reset');

        if (! $session || $session['email'] !== $request->validated('email')) {
            return redirect()->route('dashboard.password.request');
        }

        if (! $this->otpService->validate($session['email'], $session['otp'])) {
            return redirect()->route('dashboard.password.request')
                ->withErrors(['email' => __('authentication::messages.password.invalid_otp')]);
        }

        $user = User::query()->where('email', $session['email'])->firstOrFail();
        $user->forceFill([
            'password' => Hash::make($request->validated('password')),
        ])->save();

        $this->otpService->delete($session['email']);
        Session::forget('password_reset');

        Mail::to($user->email)->queue(new AdminPasswordResetSuccessMail($user));

        RecordAuditLogJob::dispatch(
            $user->id,
            'auth.password.reset',
            __('dashboard.actions.password_reset'),
            [
                'context' => 'authentication',
            ],
            'admin',
            $request->ip(),
            $request->userAgent()
        );

        return redirect()
            ->route('dashboard.login')
            ->with('success', __('authentication::messages.password.reset_success'));
    }
}
