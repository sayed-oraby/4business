<?php

namespace Modules\Notification\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SMSService
{
    public static function  send(string $mobile, string $message): bool
    {
        try {
            $response = Http::acceptJson()
                ->post(config('services.kwt-sms.send-link'), [
                    'username' => config('services.kwt-sms.username'),
                    'password' =>  config('services.kwt-sms.password'),
                    'mobile' => '965' . $mobile,
                    'lang' => 3,
                    'test' => 0,
                    'sender' => config('services.kwt-sms.senderID'),
                    'message' => $message
                ]);
            if ($response->status() > 399) {
                Log::error('Error sending SMS message to ' . $mobile);
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            Log::error('Error sending SMS message to ' . $mobile . ' because of ' . $e->getMessage());
            return false;
        }
    }
}
