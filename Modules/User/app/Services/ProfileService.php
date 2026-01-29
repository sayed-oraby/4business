<?php

namespace Modules\User\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\Activity\Services\AuditLogger;
use Modules\Setting\Services\Media\MediaUploader;
use Modules\User\Models\User;

class ProfileService
{
    public function __construct(
        protected MediaUploader $uploader,
        protected AuditLogger $audit
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateProfile(User $user, array $data): User
    {
        $user->fill([
            'name' => $data['name'],
            'mobile' => $data['mobile'] ?? null,
            'email' => $data['email'] ?? null,
            'state_id' => $data['state_id'] ?? null,
            'city_id' => $data['city_id'] ?? null,
        ])->save();

        $messageKey = 'user::users.messages.updated';
        $message = __($messageKey);

        $this->audit->log($user->id, 'users.profile_update', $message, [
            'context' => 'users',
            'notify' => false,
            'description_key' => $messageKey,
        ]);

        return $user->fresh();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updatePassword(User $user, array $data): void
    {
        if (! Hash::check($data['current_password'], $user->password)) {
            $this->throwInvalidPassword();
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        $user->tokens()->delete();

        $messageKey = 'user::users.messages.password_changed';
        $message = __($messageKey);

        $this->audit->log($user->id, 'users.password_change', $message, [
            'context' => 'users',
            'level' => 'warning',
            'notification_type' => 'alert',
            'notification_message' => $message,
            'description_key' => $messageKey,
            'notification_message_key' => $messageKey,
        ]);
    }

    public function updateAvatar(User $user, ?UploadedFile $avatar, bool $remove): ?string
    {
        if ($avatar) {
            $upload = $this->uploader->upload($avatar, 'users/avatars', [
                'max_width' => 512,
            ]);
            $user->update(['avatar' => $upload->path()]);

            $messageKey = 'user::users.messages.avatar_updated';
            $message = __($messageKey);

            $this->audit->log($user->id, 'users.avatar_change', $message, [
                'context' => 'users',
                'notify' => false,
                'description_key' => $messageKey,
            ]);

            return $user->fresh()->avatar_url;
        }

        if ($remove) {
            $user->update(['avatar' => null]);

            $messageKey = 'user::users.messages.avatar_updated';
            $message = __($messageKey);

            $this->audit->log($user->id, 'users.avatar_change', $message, [
                'context' => 'users',
                'notify' => false,
                'description_key' => $messageKey,
            ]);

            return null;
        }

        return $user->avatar_url;
    }

    public function deleteAccount(User $user, string $password): void
    {
        if (! Hash::check($password, $user->password)) {
            $this->throwInvalidPassword();
        }

        $user->tokens()->delete();
        $user->delete();

        $messageKey = 'user::users.messages.deleted';
        $message = __($messageKey);

        $this->audit->log($user->id, 'users.delete_account', $message, [
            'context' => 'users',
            'level' => 'danger',
            'notification_type' => 'alert',
            'notification_message' => $message,
            'description_key' => $messageKey,
            'notification_message_key' => $messageKey,
        ]);
    }

    protected function throwInvalidPassword(): void
    {
        throw ValidationException::withMessages([
            'current_password' => [__('validation.current_password')],
        ]);
    }
}
