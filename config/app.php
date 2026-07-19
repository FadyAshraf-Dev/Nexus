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

        'remember_me' => [
            'name' => 'remember_me',
            'duration' => '+30 days',
        ],

        'cart' => [
            'name' => 'shopping_cart',
            'duration' => '+30 days',
        ],

    ],
    'max_image_size' => 2 * 1024 * 1024,


    'urls' => [
        'base_url' => '/',
        'admin_url' => '/admin/',
        'product_uploads' => '/uploads/products/',
    ],
];