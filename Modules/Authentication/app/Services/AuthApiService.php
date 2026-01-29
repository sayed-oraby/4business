<?php

namespace Modules\Authentication\Services;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Modules\Activity\Services\AuditLogger;
use Modules\Authentication\Mail\AdminPasswordResetOtpMail;
use Modules\Authentication\Mail\AdminPasswordResetSuccessMail;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Models\User;

class AuthApiService
{
    public function __construct(
        protected PasswordResetOtpService $otpService,
        protected AuditLogger $audit
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function register(array $data): array
    {
        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'mobile' => $data['mobile'] ?? null,
                'birthdate' => $data['birthdate'] ?? null,
                'gender' => $data['gender'] ?? null,
            ]);

            event(new Registered($user));

            return $user;
        });

        $token = $user->createToken('auth-token')->plainTextToken;

        $payload = array_merge(
            $this->buildAuthPayload($user, $token),
            ['user_model' => $user]
        );

        $messageKey = 'authentication::messages.api.registered';
        $message = __($messageKey);

        $this->logAuthEvent($user->id, 'auth.register', $message, [
            'level' => 'success',
            'notification_type' => 'alert',
            'notification_message' => $message,
            'description_key' => $messageKey,
            'notification_message_key' => $messageKey,
        ]);

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    public function login(array $credentials): array
    {
        $user = User::withTrashed()
            ->where('email', $credentials['email'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password ?? '')) {
            throw ValidationException::withMessages([
                'email' => __('authentication::auth.failed'),
            ]);
        }

        if ($user->trashed()) {
            throw ValidationException::withMessages([
                'email' => __('authentication::auth.failed'),
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        $payload = $this->buildAuthPayload($user, $token);

        $messageKey = 'authentication::messages.api.logged_in';
        $message = __($messageKey);

        $this->logAuthEvent($user->id, 'auth.login', $message, [
            'level' => 'info',
            'notification_type' => 'alert',
            'notification_message' => $message,
            'description_key' => $messageKey,
            'notification_message_key' => $messageKey,
        ]);

        return $payload;
    }

    public function forgotPassword(string $email): void
    {
        $otp = $this->otpService->create($email);

        $user = User::withTrashed()->where('email', $email)->first();

        Mail::to($email)->queue(new AdminPasswordResetOtpMail($otp, $email, app()->getLocale()));

        if ($user) {
            $messageKey = 'authentication::messages.password.otp_sent';
            $messageParams = ['email' => $email];
            $message = __($messageKey, $messageParams);

            $this->logAuthEvent($user->id, 'auth.password.forgot', $message, [
                'level' => 'warning',
                'notification_type' => 'alert',
                'notification_message' => $message,
                'description_key' => $messageKey,
                'description_params' => $messageParams,
                'notification_message_key' => $messageKey,
                'notification_message_params' => $messageParams,
            ]);
        }
    }

    /**
     * @param  array{email:string,otp:string}  $data
     */
    public function verifyOtp(array $data): void
    {
        if (! $this->otpService->validate($data['email'], $data['otp'])) {
            throw ValidationException::withMessages([
                'otp' => __('authentication::messages.password.invalid_otp'),
            ]);
        }

        $user = User::withTrashed()->where('email', $data['email'])->first();

        if ($user) {
            $messageKey = 'authentication::messages.password.otp_verified';
            $message = __($messageKey);

            $this->logAuthEvent($user->id, 'auth.password.otp_verified', $message, [
                'notify' => false,
                'description_key' => $messageKey,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function resetPassword(array $data): void
    {
        if (! $this->otpService->validate($data['email'], $data['otp'])) {
            throw ValidationException::withMessages([
                'otp' => __('authentication::messages.password.invalid_otp'),
            ]);
        }

        $user = User::where('email', $data['email'])->firstOrFail();

        DB::transaction(function () use ($user, $data) {
            $user->forceFill([
                'password' => Hash::make($data['password']),
            ])->save();

            $user->tokens()->delete();
        });

        $this->otpService->delete($data['email']);

        Mail::to($user->email)->queue(new AdminPasswordResetSuccessMail($user));

        $messageKey = 'authentication::messages.password.reset_success';
        $message = __($messageKey);

        $this->logAuthEvent($user->id, 'auth.password.reset', $message, [
            'level' => 'warning',
            'notification_type' => 'alert',
            'notification_message' => $message,
            'description_key' => $messageKey,
            'notification_message_key' => $messageKey,
        ]);
    }

    protected function buildAuthPayload(User $user, string $token): array
    {
        // Laravel Sanctum tokens expire in 1 year by default
        $expiresAt = now()->addYear()->format('Y-m-d H:i:s');
        
        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => (new UserResource($user))->resolve(),
            'expires_at' => $expiresAt,
        ];
    }
    protected function logAuthEvent(?int $userId, string $action, ?string $description = null, array $properties = []): void
    {
        $this->audit->log($userId, $action, $description, array_merge([
            'context' => 'auth',
        ], $properties));
    }
}
