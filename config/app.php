<?php

return [

    'name' => 'Nexus',

    'debug' => true,

    'session_lifetime' => 30 * 24 * 60 * 60,

    'paths' => [

        'product_uploads' => dirname(__DIR__) . '/public/uploads/products/',

    ],
    'allowed_image_types' => [

        'image/jpeg',
        'image/png',
        'image/webp',
        'image/avif',

    ],
    'cookies' => [
        'name' => 'remember_me',
        'duration' => "+30days",
    ],

    'max_image_size' => 2 * 1024 * 1024,

    'pagination' => [

        'per_page' => 15,

    ],

    'urls' => [
        'base_url' => '/',
        'admin_url' => '/admin/',
        'product_uploads' => '/uploads/products/',
    ],
];