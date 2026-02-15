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

    'stripe' => [
        'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'SAR'),
    ],

    'nafath' => [
        'api_url' => env('NAFATH_API_URL', 'https://mock-service.api.elm.sa/nafath'),
        'app_id' => env('NAFATH_APP_ID', 'w9kyh348'),
        'app_key' => env('NAFATH_APP_KEY', '87e50ef41b694ef7ae8b0c9831fd530d'),
        'timeout' => env('NAFATH_TIMEOUT', 300),
        'bypass' => env('NAFATH_BYPASS', false),
    ],

    // NELC FutureX Integration
    'futurex' => [
        // SSO Configuration
        'client_id' => env('FUTUREX_CLIENT_ID'),
        'client_secret' => env('FUTUREX_CLIENT_SECRET'),
        'redirect' => env('FUTUREX_REDIRECT_URI'),
        'base_url' => env('FUTUREX_BASE_URL', 'https://api.futurex.sa'),
        'authorize_url' => env('FUTUREX_AUTHORIZE_URL'),
        'token_url' => env('FUTUREX_TOKEN_URL'),
        'userinfo_url' => env('FUTUREX_USERINFO_URL'),

        // API Configuration
        'api_base_url' => env('FUTUREX_API_URL', 'https://api.futurex.sa/v1'),
        'api_key' => env('FUTUREX_API_KEY'),
        'provider_id' => env('FUTUREX_PROVIDER_ID'),
        'webhook_secret' => env('FUTUREX_WEBHOOK_SECRET'),
    ],

];
