<?php

namespace Modules\Activity\Services;

use Illuminate\Support\Facades\Log;
use Modules\Activity\Events\SystemNotificationCreated;
use Modules\Activity\Models\SystemNotification;
use Modules\Activity\Traits\DirectBroadcast;

class NotificationPublisher
{
    use DirectBroadcast;

    /**
     * Publish a notification and broadcast it.
     */
    public function publish(array $data): SystemNotification
    {
        // Create the notification in database
        $notification = SystemNotification::create([
            'type' => $data['type'] ?? 'alert',
            'level' => $data['level'] ?? 'info',
            'title' => $data['title'],
            'message' => $data['message'] ?? null,
            'payload' => $data['payload'] ?? [],
            'user_id' => $data['user_id'] ?? null,
            'notifiable_id' => $data['notifiable_id'] ?? null,
            'channel' => $data['channel'] ?? null,
        ]);

        // Try Laravel's event broadcasting first
        try {
            SystemNotificationCreated::dispatch($notification);
            Log::info('📢 NotificationPublisher: Event dispatched', ['id' => $notification->id]);
        } catch (\Exception $e) {
            Log::warning('NotificationPublisher: Event dispatch failed, trying direct broadcast', [
                'error' => $e->getMessage(),
            ]);

            // Fallback: Direct broadcast via Pusher/Reverb SDK
            $this->tryDirectBroadcast($notification);
        }

        return $notification;
    }

    /**
     * Attempt direct broadcast as fallback.
     */
    protected function tryDirectBroadcast(SystemNotification $notification): void
    {
        $data = [
            'id' => $notification->id,
            'uuid' => $notification->uuid,
            'type' => $notification->type,
            'category' => $notification->category,
            'level' => $notification->level,
            'title' => $notification->title,
            'message' => $notification->message,
            'payload' => $notification->payload,
            'created_at' => $notification->created_at?->toDateTimeString(),
        ];

        $this->safeBroadcast('private-dashboard.notifications', $data, 'SystemNotificationCreated');
    }
}

