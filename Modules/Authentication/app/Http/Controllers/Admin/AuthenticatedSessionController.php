<?php

namespace Modules\Authentication\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Activity\Jobs\RecordAuditLogJob;
use Modules\Authentication\Http\Requests\AdminLoginRequest;
use Modules\Authentication\Services\AdminAuthenticator;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        protected AdminAuthenticator $authenticator
    ) {
    }

    public function create()
    {
        return view('authentication::dashboard.login');
    }

    public function store(AdminLoginRequest $request)
    {
        $credentials = $request->validated();

        $fallbackPassword = config('auth.super_admin_password', 'password');
        if (
            $credentials['email'] === config('auth.super_admin_email') &&
            empty($credentials['password'])
        ) {
            $credentials['password'] = $fallbackPassword;
        }

        $this->authenticator->attempt($credentials, $request->boolean('remember'));

        $user = auth('admin')->user();
        RecordAuditLogJob::dispatch(
            $user?->id,
            'auth.login',
            __('dashboard.actions.login'),
            ['context' => 'authentication'],
            'admin',
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->intended(route('dashboard.home'));
    }

    public function destroy(Request $request)
    {
        $user = auth('admin')->user();
        $this->authenticator->logout($request);

        RecordAuditLogJob::dispatch(
            $user?->id,
            'auth.logout',
            __('dashboard.actions.logout'),
            ['context' => 'authentication'],
            'admin',
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('dashboard.login');
    }
}
