<?php

namespace Modules\Activity\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Activity\Models\SystemNotification;

class SystemNotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public SystemNotification $notification
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('dashboard.notifications'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'uuid' => $this->notification->uuid,
            'type' => $this->notification->type,
            'category' => $this->notification->category,
            'level' => $this->notification->level,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'payload' => $this->notification->payload,
            'created_at' => $this->notification->created_at?->toDateTimeString(),
        ];
    }

    /**
     * The name of the queue connection to use when broadcasting the event.
     */
    public function broadcastConnection(): ?string
    {
        return null; // Use default connection
    }

    /**
     * Handle a broadcast failure.
     */
    public function broadcastFailed(\Exception $e): void
    {
        Log::warning('SystemNotificationCreated broadcast failed', [
            'notification_id' => $this->notification->id,
            'error' => $e->getMessage(),
        ]);
        
        // The notification is still saved in database, user can see it on refresh
    }
}

