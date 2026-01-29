<?php

return [
    'server_key' => env('FCM_SERVER_KEY'),
    'endpoint' => env('FCM_ENDPOINT', 'https://fcm.googleapis.com/fcm/send'),
];
