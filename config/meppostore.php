<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Meppostore API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Meppostore API integration
    | API URL: https://a-api.meppostore.id
    |
    */

    'api_url' => env('MEPPOSTORE_API_URL', 'https://a-api.meppostore.id'),
    'api_key' => env('MEPPOSTORE_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Timeout Configuration
    |--------------------------------------------------------------------------
    */
    'timeout' => 30,
    'connect_timeout' => 10,

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | Common payment method IDs from Meppostore
    |
    */
    'payment_methods' => [
        40 => 'QRIS (Untuk Semua BANK dan e-Wallet)',
        // Add more as discovered from API
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'categories_ttl' => 3600,    // 1 hour
        'products_ttl' => 1800,       // 30 minutes
        'payment_methods_ttl' => 3600, // 1 hour
    ],
];
