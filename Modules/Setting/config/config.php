<?php

return [
    'name' => 'Setting',

    'defaults' => [
        'app_name' => [
            'en' => env('APP_NAME', 'GavanKit'),
            'ar' => env('APP_NAME_AR', 'جافان كت'),
        ],
        'app_tagline' => 'Admin Access Portal',
        'app_description' => [
            'en' => 'Modern modular Laravel dashboard powered by Metronic.',
            'ar' => 'لوحة تحكم معيارية حديثة مبنية على لارافيل وميتروإنيك.',
        ],
        'app_keywords' => 'gavankit, admin, dashboard, modular, metronic',
        'app_url' => env('APP_URL', 'http://localhost'),
        'app_slogan' => 'Fast, Efficient and Productive',
        'logo' => null,
        'logo_white' => null,
        'logo_mobile' => null,
        'favicon' => null,
        'default_locale' => env('APP_LOCALE', 'en'),
        'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
        'supported_locales' => [
            'en' => [
                'name' => 'English',
                'native' => 'English',
                'dir' => 'ltr',
                'emoji' => '🇺🇸',
            ],
            'ar' => [
                'name' => 'Arabic',
                'native' => 'العربية',
                'dir' => 'rtl',
                'emoji' => '🇸🇦',
            ],
        ],
        'contact' => [
            'address' => [
                'en' => 'Sharq, Kuwait City',
                'ar' => 'شرق، مدينة الكويت',
            ],
            'inbox_email' => 'techgavan@gmail.com',
            'whatsapp' => '50872712',
            'phone' => '50872712',
            'support_line' => '50872712',
        ],
        'mail' => [
            'driver' => env('MAIL_MAILER', 'smtp'),
            'encryption' => env('MAIL_ENCRYPTION', 'ssl'),
            'host' => env('MAIL_HOST', 'smtp.zeptomail.com'),
            'port' => env('MAIL_PORT', 465),
            'username' => env('MAIL_USERNAME', 'emailapikey'),
            'password' => env('MAIL_PASSWORD'),
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'info@gavan-tech.com'),
                'name' => env('MAIL_FROM_NAME', 'Gavan Kit'),
            ],
        ],
        'branding' => [
            'logo' => null,
            'logo_white' => null,
            'favicon' => null,
            'footer' => '2025© Laravel',
        ],
        'social_links' => [
            'facebook' => 'https://www.facebook.com/3allemnygroup',
            'twitter' => 'https://www.tiktok.com/@3allemny_group?is_from_webapp=1&sender_device=pc',
            'instagram' => 'https://www.instagram.com/allemny_group',
            'youtube' => 'https://www.youtube.com/@3allemnygroup',
            'snapchat' => '#',
            'tiktok' => 'https://www.tiktok.com/@3allemny_group?is_from_webapp=1&sender_device=pc',
        ],
        'custom_code' => [
            'head_css' => '',
            'head_js' => '',
            'body_js' => '',
        ],
    ],
];
