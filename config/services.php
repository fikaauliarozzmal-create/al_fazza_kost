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

    'dana' => [
    'environment' => env('DANA_ENV', 'sandbox'),
    'base_url' => env('DANA_BASE_URL'),

    'client_id' => env('DANA_CLIENT_ID'),
    'client_secret' => env('DANA_CLIENT_SECRET'),
    'merchant_id' => env('DANA_MERCHANT_ID'),

    'private_key_path' => env('DANA_PRIVATE_KEY_PATH'),
    'public_key_path' => env('DANA_PUBLIC_KEY_PATH'),

    'notify_url' => env('DANA_NOTIFY_URL'),
    'return_url' => env('DANA_RETURN_URL'),
    'webhook_path' => env('DANA_WEBHOOK_PATH', '/v1.0/debit/notify'),

    'channel_id' => env('DANA_CHANNEL_ID'),
    'mcc' => env('DANA_MCC'),

    'sub_merchant_id' => env('DANA_SUB_MERCHANT_ID'),
    'external_store_id' => env('DANA_EXTERNAL_STORE_ID'),

    'expiry_minutes' => env('DANA_EXPIRY_MINUTES', 30),
],

];
