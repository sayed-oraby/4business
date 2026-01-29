<?php

namespace Modules\Activity\Services;

use Illuminate\Support\Facades\DB;
use Modules\Activity\Models\NotificationDevice;
use Modules\User\Models\User;

class NotificationDeviceService
{
    public function register(?User $user, array $payload): NotificationDevice
    {
        $attributes = ['device_uuid' => $payload['device_uuid']];

        $values = [
            'device_token' => $payload['device_token'],
            'device_type' => $payload['device_type'],
            'app_version' => $payload['app_version'] ?? null,
            'language' => $payload['language'] ?? app()->getLocale(),
            'notifications_enabled' => $payload['notifications_enabled'] ?? true,
            'last_seen_at' => now(),
        ];

        // Set user_id and guest_uuid based on authentication
        if ($user) {
            // Logged in user
            $values['user_id'] = $user->id;
            $values['guest_uuid'] = null;
        } else {
            // Guest user (including after logout)
            $values['user_id'] = null;
            $values['guest_uuid'] = $payload['guest_uuid'] ?? null;
        }

        return DB::transaction(function () use ($attributes, $values) {
            $device = NotificationDevice::withTrashed()
                ->lockForUpdate()
                ->firstOrNew($attributes);

            if (! $device->exists) {
                $device->device_uuid = $attributes['device_uuid'];
            }

            $device->fill($values);
            $device->forceFill(['deleted_at' => null]);
            $device->save();

            return $device->fresh();
        });
    }

    public function update(NotificationDevice $device, array $payload, ?User $user = null): NotificationDevice
    {
        $updates = array_filter([
            'device_token' => $payload['device_token'] ?? null,
            'device_type' => $payload['device_type'] ?? null,
            'app_version' => $payload['app_version'] ?? null,
            'language' => $payload['language'] ?? null,
            'notifications_enabled' => $payload['notifications_enabled'] ?? null,
        ], fn ($value) => $value !== null);

        if ($user && ! $device->user_id) {
            $updates['user_id'] = $user->id;
            $updates['guest_uuid'] = null;
        }

        $updates['last_seen_at'] = now();

        $device->update($updates);

        return $device->fresh();
    }

    public function delete(NotificationDevice $device): void
    {
        $device->delete();
    }

    /**
     * @param  array<string, mixed>  $message
     */
    public function sendToUser(User $user, array $message): void
    {
        $tokens = NotificationDevice::query()
            ->where('user_id', $user->id)
            ->where('notifications_enabled', true)
            ->pluck('device_token')
            ->all();

        if (empty($tokens)) {
            return;
        }

        app(FcmService::class)->send($tokens, $message);
    }
}
