<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CORS (Cross-Origin Resource Sharing) Configuration
    |--------------------------------------------------------------------------
    | Cho phép React SPA trên Netlify gọi API về Laravel trên Render.
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        // Địa chỉ Netlify của bạn (sửa lại sau khi deploy lên Netlify)
        env('FRONTEND_URL', 'http://localhost:5173'),
    ],

    'allowed_origins_patterns' => [
        // Cho phép tất cả subdomain của netlify.app khi dev
        '#^https://.*\.netlify\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
