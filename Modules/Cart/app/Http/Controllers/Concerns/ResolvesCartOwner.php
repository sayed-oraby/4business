<?php

namespace Modules\Cart\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\User\Models\User;

trait ResolvesCartOwner
{
    protected function resolveUser(Request $request): ?User
    {
        $guards = array_filter(array_unique([
            $request->attributes->get('auth_guard'),
            'sanctum',
            config('auth.defaults.guard'),
            'web',
            'api',
        ]));

        foreach ($guards as $guard) {
            $user = Auth::guard($guard)->user();
            if ($user instanceof User) {
                return $user;
            }
        }

        $fallback = $request->user();

        return $fallback instanceof User ? $fallback : null;
    }

    protected function resolveGuestUuid(Request $request): ?string
    {
        return $request->string('guest_uuid')->toString()
            ?: $request->header('X-Guest-UUID')
            ?: null;
    }
}
