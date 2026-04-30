<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'wordpress' => [
        'url' => env('WORDPRESS_URL'),
        'consumer_key' => env('WC_CONSUMER_KEY'),
        'consumer_secret' => env('WC_CONSUMER_SECRET'),
        'api_token' => env('WORDPRESS_API_TOKEN'),
        'webhook_secret' => env('WORDPRESS_WEBHOOK_SECRET'),
        'wc_key' => env('WC_CONSUMER_KEY'),
        'wc_secret' => env('WC_CONSUMER_SECRET'),
        
        // Configuration de synchronisation
        'sync_enabled' => env('WORDPRESS_SYNC_ENABLED', true),
        'verify_ssl' => env('WORDPRESS_VERIFY_SSL', true),
        'timeout' => env('WORDPRESS_TIMEOUT', 30),
        'retry_attempts' => env('WORDPRESS_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('WORDPRESS_RETRY_DELAY', 1000), // milliseconds
    ],

    'paps' => [
        'api_url' => env('PAPS_API_URL', 'https://api.papslogistics.com'),
        'client_id' => env('PAPS_CLIENT_ID'),
        'client_secret' => env('PAPS_CLIENT_SECRET'),
        'default_vehicle_type' => env('PAPS_DEFAULT_VEHICLE_TYPE', 'SCOOTER'),
        'default_delivery_type' => env('PAPS_DEFAULT_DELIVERY_TYPE', 'STANDARD'),
        'webhook_secret' => env('PAPS_WEBHOOK_SECRET'),
        'timeout' => env('PAPS_TIMEOUT', 30),
    ],
];  