<?php

return [
    'api' => [
        'url' => env('WORDPRESS_API_URL', 'https://custom-size.test'),
        'token' => env('WORDPRESS_API_TOKEN'),
        'timeout' => 30,
        'retry' => [
            'times' => 3,
            'sleep' => 100,
        ],
    ],
    
    'ssl' => [
        'verify' => env('WORDPRESS_VERIFY_SSL', env('APP_ENV') === 'production'),
        'cert_path' => env('WORDPRESS_SSL_CERT', base_path('laragon.crt')),
    ],
    
    'endpoints' => [
        'stats' => '/wp-json/mp/v2/stats',
        'orders' => '/wp-json/mp/v2/orders',
        'products' => '/wp-json/mp/v2/products',
    ],
];