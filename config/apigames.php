<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ApiGames Merchant Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for ApiGames API integration
    | API Documentation: https://apigames.id
    |
    */

    'merchant_id' => env('APIGAMES_MERCHANT_ID'),
    'secret_key' => env('APIGAMES_SECRET_KEY'),
    'api_url' => env('APIGAMES_API_URL', 'https://v1.apigames.id'),
    'environment' => env('APIGAMES_ENVIRONMENT', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Game Code Mapping (Short Code => API Code)
    |--------------------------------------------------------------------------
    |
    | Supported games for username check: mobilelegend, freefire, higgs
    |
    */
    'username_check_games' => ['mobilelegend', 'freefire', 'higgs'],

    /*
    |--------------------------------------------------------------------------
    | Engine Configuration
    |--------------------------------------------------------------------------
    |
    | Available engines for different game providers
    |
    */
    'engines' => [
        'higgs' => 'Higgs Domino',
        'kiosgamer' => 'Kiosgamer',
        'smileone' => 'Smile One',
        'unipin' => 'Unipin',
        'unipinbr' => 'Unipin Brazil',
        'unipinmy' => 'Unipin Malaysia',
        'gamepoint' => 'GamePoint',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    */
    'endpoints' => [
        'info' => '/merchant/{merchant_id}',
        'check_connection' => '/merchant/{merchant_id}/cek-koneksi',
        'check_username' => '/merchant/{merchant_id}/cek-username/{game_code}',
        'transaction' => '/v2/transaksi',
        'transaction_irs' => '/v2/transaksi-irs',
        'transaction_otomax' => '/v2/transaksi-otomax',
        'product_list' => '/merchant/{merchant_id}/produk',
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeout Configuration
    |--------------------------------------------------------------------------
    */
    'timeout' => 30,
    'connect_timeout' => 10,

    /*
    |--------------------------------------------------------------------------
    | Product Mapping
    |--------------------------------------------------------------------------
    |
    | Common game codes for product sync
    |
    */
    'game_codes' => [
        'ml' => 'Mobile Legends',
        'ff' => 'Free Fire',
        'pubg' => 'PUBG Mobile',
        'cod' => 'Call of Duty',
        'aov' => 'Arena of Valor',
        'valorant' => 'Valorant',
        'hok' => 'Honor of Kings',
        'genshin' => 'Genshin Impact',
        'hsr' => 'Honkai Star Rail',
        'higgs' => 'Higgs Domino',
    ],
];
