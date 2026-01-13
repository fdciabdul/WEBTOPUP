<?php

return [
    'username' => env('DIGIFLAZZ_USERNAME'),
    'api_key' => env('DIGIFLAZZ_API_KEY'),
    'webhook_secret' => env('DIGIFLAZZ_WEBHOOK_SECRET'),
    'environment' => env('DIGIFLAZZ_ENVIRONMENT', 'production'),
    
    'endpoints' => [
        'production' => 'https://api.digiflazz.com/v1',
        'development' => 'https://api.digiflazz.com/v1',
    ],
    
    'timeout' => 30,
    'retry_times' => 3,
    'retry_delay' => 1000,
];
