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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'zoom' => [
        // Server-to-Server OAuth (for API)
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),

        // SDK App (for Web SDK)
        'sdk_key' => env('ZOOM_SDK_KEY'),
        'sdk_secret' => env('ZOOM_SDK_SECRET'),

        // Legacy JWT support (not used)
        'api_key' => env('ZOOM_API_KEY'),
        'api_secret' => env('ZOOM_API_SECRET'),
        'webhook_secret_token' => env('ZOOM_WEBHOOK_SECRET_TOKEN'),
    ],

    'tamara' => [
        'api_url' => env('TAMARA_API_URL', 'https://api.tamara.co'),
        'api_token' => env('TAMARA_API_TOKEN'),
        'notification_token' => env('TAMARA_NOTIFICATION_TOKEN'),
        'merchant_url' => env('TAMARA_MERCHANT_URL'),
        'webhook_secret' => env('TAMARA_WEBHOOK_SECRET'),
    ],

    'paytabs' => [
        'profile_id' => env('PAYTABS_PROFILE_ID'),
        'server_key' => env('PAYTABS_SERVER_KEY'),
        'currency' => env('PAYTABS_CURRENCY', 'SAR'),
        'region' => env('PAYTABS_REGION', 'SAU'),
    ],

];
