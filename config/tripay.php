<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tripay H2H API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Tripay H2H API settings. You can get your
    | API key from your Tripay dashboard under Profile > API & Callback.
    |
    */

    'api_key' => env('TRIPAY_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | API Mode
    |--------------------------------------------------------------------------
    |
    | Set to true for sandbox mode (testing) or false for production mode.
    | In sandbox mode, all transactions are simulated with dummy data.
    |
    */

    'sandbox' => env('TRIPAY_SANDBOX', true),

    /*
    |--------------------------------------------------------------------------
    | API Base URLs
    |--------------------------------------------------------------------------
    |
    | The base URLs for Tripay API endpoints. You should not need to change
    | these unless Tripay updates their API endpoints.
    |
    */

    'base_urls' => [
        'sandbox' => env('TRIPAY_SANDBOX_URL', 'https://tripay.id/api-sandbox/v2'),
        'production' => env('TRIPAY_PRODUCTION_URL', 'https://tripay.id/api/v2'),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for HTTP client used to make API requests.
    |
    */

    'http' => [
        'timeout' => env('TRIPAY_TIMEOUT', 30),
        'retry_times' => env('TRIPAY_RETRY_TIMES', 3),
        'retry_sleep' => env('TRIPAY_RETRY_SLEEP', 100), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable or disable API request/response logging for debugging purposes.
    |
    */

    'logging' => [
        'enabled' => env('TRIPAY_LOGGING', false),
        'channel' => env('TRIPAY_LOG_CHANNEL', 'daily'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configure caching for API responses to improve performance.
    |
    */

    'cache' => [
        'enabled' => env('TRIPAY_CACHE_ENABLED', true),
        'ttl' => env('TRIPAY_CACHE_TTL', 300), // seconds (5 minutes)
        'prefix' => env('TRIPAY_CACHE_PREFIX', 'tripay_'),
    ],
];