<?php

namespace Modules\Authentication\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordResetOtpService
{
    protected int $ttlMinutes;

    public function __construct()
    {
        $this->ttlMinutes = (int) config('authentication.password.otp_ttl', 10);
    }

    public function create(string $email): string
    {
        $otp = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        return $otp;
    }

    public function validate(string $email, string $otp): bool
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $record) {
            return false;
        }

        if (Carbon::parse($record->created_at)->lt(now()->subMinutes($this->ttlMinutes))) {
            return false;
        }

        return Hash::check($otp, $record->token);
    }

    public function delete(string $email): void
    {
        DB::table('password_reset_tokens')->where('email', $email)->delete();
    }
}
