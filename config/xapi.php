<?php

return [
    /*
    |--------------------------------------------------------------------------
    | xAPI (Experience API) Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for xAPI (Experience API / Tin Can API) integration with
    | NELC FutureX LRS (Learning Record Store).
    |
    */

    'enabled' => env('XAPI_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | LRS (Learning Record Store) Configuration
    |--------------------------------------------------------------------------
    */

    'lrs_endpoint' => env('XAPI_LRS_ENDPOINT'),        // FutureX LRS URL
    'lrs_username' => env('XAPI_LRS_USERNAME'),         // Basic auth key
    'lrs_password' => env('XAPI_LRS_PASSWORD'),         // Basic auth secret

    /*
    |--------------------------------------------------------------------------
    | xAPI Version
    |--------------------------------------------------------------------------
    */

    'version' => '1.0.3',                                // xAPI version

    /*
    |--------------------------------------------------------------------------
    | Platform Information
    |--------------------------------------------------------------------------
    */

    'platform_iri' => env('XAPI_PLATFORM_IRI', 'https://lms.example.sa'),
    'platform_name_ar' => env('XAPI_PLATFORM_NAME_AR', 'نظام إدارة التعلم'),
    'platform_name_en' => env('XAPI_PLATFORM_NAME_EN', 'LMS Platform'),

    /*
    |--------------------------------------------------------------------------
    | Batch Processing Configuration
    |--------------------------------------------------------------------------
    */

    'batch_size' => env('XAPI_BATCH_SIZE', 50),         // Statements per batch
    'retry_max' => env('XAPI_RETRY_MAX', 3),
    'retry_delay' => env('XAPI_RETRY_DELAY', 300),      // seconds

    /*
    |--------------------------------------------------------------------------
    | Statement Retention
    |--------------------------------------------------------------------------
    */

    'statement_retention_months' => env('XAPI_STATEMENT_RETENTION_MONTHS', 24),
];
