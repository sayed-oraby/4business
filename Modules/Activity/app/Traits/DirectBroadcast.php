<?php

namespace Modules\Activity\Traits;

use Illuminate\Support\Facades\Log;

/**
 * Trait for broadcasting notifications directly via Pusher/Reverb SDK.
 * This provides more control and error handling than Laravel's broadcast system.
 */
trait DirectBroadcast
{
    /**
     * Broadcast notification directly via Pusher/Reverb SDK.
     * 
     * @param array|string $channels Channel(s) to broadcast to
     * @param array $data Data to broadcast
     * @param string $eventName Event name
     * @return bool Success status
     */
    protected function broadcastDirectly(
        array|string $channels,
        array $data,
        string $eventName = 'App\\Events\\SystemNotificationCreated'
    ): bool {
        $channels = is_array($channels) ? $channels : [$channels];
        
        try {
            $broadcaster = $this->getBroadcaster();
            
            if (!$broadcaster) {
                Log::warning('DirectBroadcast: No broadcaster configured');
                return false;
            }

            // Ensure data has required fields
            $data = array_merge([
                'id' => $data['id'] ?? uniqid('notification_'),
                'created_at' => $data['created_at'] ?? now()->toDateTimeString(),
            ], $data);

            // Broadcast to each channel
            foreach ($channels as $channel) {
                $broadcaster->trigger($channel, $eventName, $data);
                Log::info("📢 DirectBroadcast: Sent to channel '{$channel}'", ['event' => $eventName]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('DirectBroadcast: Failed to broadcast', [
                'error' => $e->getMessage(),
                'channels' => $channels,
            ]);
            return false;
        }
    }

    /**
     * Broadcast to dashboard notifications channel.
     */
    protected function broadcastToDashboard(array $data, string $eventName = 'SystemNotificationCreated'): bool
    {
        return $this->broadcastDirectly(
            'private-dashboard.notifications',
            $data,
            $eventName
        );
    }

    /**
     * Broadcast to a specific user's private channel.
     */
    protected function broadcastToUser(int $userId, array $data, string $eventName = 'SystemNotificationCreated'): bool
    {
        return $this->broadcastDirectly(
            "private-App.User.{$userId}",
            $data,
            $eventName
        );
    }

    /**
     * Get the Pusher/Reverb broadcaster instance.
     */
    protected function getBroadcaster(): ?\Pusher\Pusher
    {
        $connection = config('broadcasting.default');
        
        if ($connection === 'null') {
            return null;
        }

        $config = config("broadcasting.connections.{$connection}");
        
        if (!$config) {
            return null;
        }

        // Determine if using Reverb (acts as Pusher-compatible server)
        $isReverb = $connection === 'reverb';
        
        $options = [
            'cluster' => $config['options']['cluster'] ?? 'mt1',
            'useTLS' => ($config['options']['scheme'] ?? 'https') === 'https',
        ];

        // For Reverb, we need to specify the host explicitly
        if ($isReverb) {
            $options['host'] = $config['options']['host'] ?? '127.0.0.1';
            $options['port'] = $config['options']['port'] ?? 8080;
            $options['scheme'] = $config['options']['scheme'] ?? 'http';
            $options['useTLS'] = $options['scheme'] === 'https';
            
            // Reverb uses its own endpoint format
            $options['curl_options'] = [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ];
        }

        return new \Pusher\Pusher(
            $config['key'],
            $config['secret'],
            $config['app_id'],
            $options
        );
    }

    /**
     * Try broadcasting with fallback - if WebSocket fails, notification is still saved.
     */
    protected function safeBroadcast(array|string $channels, array $data, string $eventName = 'SystemNotificationCreated'): void
    {
        try {
            $this->broadcastDirectly($channels, $data, $eventName);
        } catch (\Throwable $e) {
            // Log but don't fail - notification will still be in database
            Log::warning('SafeBroadcast: Broadcasting failed, notification saved to database only', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
