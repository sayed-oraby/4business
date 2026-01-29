<?php

return [
    'name' => 'Shipping',

    'cache' => [
        'ttl' => env('SHIPPING_CACHE_TTL', 60 * 60 * 24), // 1 day
    ],

    'countries' => [
        // override or seed specific countries here if needed
    ],

    'locations' => [
        // 'KW' => [
        //     'states' => [
        //         ['code' => 'AH', 'name_en' => 'Al Ahmadi', 'name_ar' => 'الأحمدي'],
        //     ],
        //     'cities' => [
        //         ['code' => 'KW-KWT', 'state_code' => 'KU', 'name_en' => 'Kuwait City', 'name_ar' => 'مدينة الكويت'],
        //     ],
        // ],
    ],
];
