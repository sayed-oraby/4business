<?php

return [
    'name' => 'Core',

    'cache' => [
        'settings_key' => env('SETTINGS_CACHE_KEY', 'core.settings.cache'),
    ],

    'localization' => [
        'cookie_name' => env('APP_LOCALE_COOKIE', 'gavankit_locale'),
        'cookie_lifetime_minutes' => (int) env('APP_LOCALE_COOKIE_MINUTES', 60 * 24 * 365 * 3),
        'redirect_query_key' => 'redirect',
        'supported_locales' => [],
    ],
];
