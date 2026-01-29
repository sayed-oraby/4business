<?php

namespace Modules\Authentication\Services;

use Modules\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthenticator
{
    public function attempt(array $credentials, bool $remember = false): void
    {
        $this->ensureDefaultAdminExists($credentials);

        if (! Auth::guard('admin')->attempt(
            [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ],
            $remember
        )) {
            throw ValidationException::withMessages([
                'email' => __('authentication::auth.failed'),
            ]);
        }

        session()->regenerate();
    }

    public function logout(Request $request): void
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    protected function ensureDefaultAdminExists(array $credentials): void
    {
        $email = $credentials['email'] ?? null;

        if (! $email || $email !== config('auth.super_admin_email')) {
            return;
        }

        $user = User::query()->where('email', $email)->first();

        if ($user) {
            return;
        }

        User::query()->create([
            'name' => 'Super Admin',
            'email' => $email,
            'password' => Hash::make(config('auth.super_admin_password', 'password')),
        ]);
    }
}
