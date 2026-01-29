<?php

namespace Modules\Authentication\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FrontendPasswordResetOtpService
{
    protected int $ttlMinutes;

    public function __construct()
    {
        $this->ttlMinutes = (int) config('authentication.password.otp_ttl', 10);
    }

    /**
     * Create OTP for mobile number
     */
    public function create(string $mobile): string
    {
        // Generate 4-digit OTP for frontend (mobile)
        $otp = (string) random_int(1000, 9999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $mobile], // Using email column for mobile
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        return $otp;
    }

    /**
     * Validate OTP for mobile number
     */
    public function validate(string $mobile, string $otp): bool
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $mobile) // Using email column for mobile
            ->first();

        if (! $record) {
            return false;
        }

        if (Carbon::parse($record->created_at)->lt(now()->subMinutes($this->ttlMinutes))) {
            return false;
        }

        return Hash::check($otp, $record->token);
    }

    /**
     * Delete OTP for mobile number
     */
    public function delete(string $mobile): void
    {
        DB::table('password_reset_tokens')->where('email', $mobile)->delete();
    }
}
