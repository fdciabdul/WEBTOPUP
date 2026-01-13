<?php

return [
    'provider' => env('WHATSAPP_PROVIDER', 'fonnte'),
    
    'fonnte' => [
        'api_key' => env('WHATSAPP_FONNTE_API_KEY'),
        'sender' => env('WHATSAPP_FONNTE_SENDER'),
        'url' => 'https://api.fonnte.com/send',
    ],
    
    'wablas' => [
        'api_key' => env('WHATSAPP_WABLAS_API_KEY'),
        'domain' => env('WHATSAPP_WABLAS_DOMAIN'),
        'sender' => env('WHATSAPP_WABLAS_SENDER'),
    ],
    
    'timeout' => 30,
];
