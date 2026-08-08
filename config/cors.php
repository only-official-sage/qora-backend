 <?php

// return [
//     'paths' => ['api/*', 'speed', 'sanctum/csrf-cookie'],
//     'allowed_methods' => ['*'],
//     'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],
//     'allowed_origins_patterns' => [],
//     'allowed_headers' => ['*'],
//     'exposed_headers' => [],
//     'max_age' => 0,
//     'supports_credentials' => true,
// ]; 

return [
    'paths' => ['api/*', 'speed', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'https://qora-real.vercel.app',
    ],

    'allowed_origins_patterns' => [
        '^https://qora-real-.*\.vercel\.app$',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
