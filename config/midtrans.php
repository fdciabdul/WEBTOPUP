<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'environment' => env('MIDTRANS_ENVIRONMENT', 'production'),
    'is_sanitized' => env('MIDTRANS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_3DS', true),
    
    'endpoints' => [
        'snap' => [
            'production' => 'https://app.midtrans.com/snap/v1',
            'sandbox' => 'https://app.sandbox.midtrans.com/snap/v1',
        ],
        'api' => [
            'production' => 'https://api.midtrans.com/v2',
            'sandbox' => 'https://api.sandbox.midtrans.com/v2',
        ],
    ],
    
    'payment_expiry' => 24,
    'enabled_payments' => [
        'credit_card',
        'gopay',
        'shopeepay',
        'bca_va',
        'bni_va',
        'bri_va',
        'permata_va',
        'other_va',
        'alfamart',
        'indomaret',
        'qris',
    ],
];
