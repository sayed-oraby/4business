<?php

namespace Modules\Activity\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected ?string $serverKey;

    protected string $endpoint;

    public function __construct()
    {
        $this->serverKey = config('activity.fcm.server_key');
        $this->endpoint = config('activity.fcm.endpoint', 'https://fcm.googleapis.com/fcm/send');
    }

    /**
     * @param  array<int, string>  $tokens
     * @param  array<string, mixed>  $message
     */
    public function send(array $tokens, array $message): void
    {
        if (empty($tokens) || blank($this->serverKey)) {
            return;
        }

        try {
            Http::withHeaders([
                'Authorization' => 'key='.$this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->endpoint, [
                'registration_ids' => $tokens,
                'notification' => [
                    'title' => $message['title'] ?? config('app.name'),
                    'body' => $message['body'] ?? '',
                ],
                'data' => $message['data'] ?? [],
            ])->throw();
        } catch (\Throwable $e) {
            Log::warning('FCM send failed: '.$e->getMessage());
        }
    }
}
