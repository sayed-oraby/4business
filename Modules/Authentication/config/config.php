<?php

return [
    'name' => 'Authentication',
    
    'password' => [
        'otp_ttl' => env('PASSWORD_OTP_TTL', 10), // OTP validity in minutes
    ],
];
