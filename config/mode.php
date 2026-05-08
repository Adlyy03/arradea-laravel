<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mode Switching Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for buyer/seller mode switching feature.
    |
    */

    'default_mode' => env('MODE_DEFAULT', 'buyer'),

    'modes' => [
        'buyer' => [
            'label' => 'Mode Buyer',
            'icon' => '🛒',
            'description' => 'Belanja produk dari seller',
            'dashboard_route' => 'buyer.dashboard',
        ],
        'seller' => [
            'label' => 'Mode Seller',
            'icon' => '🏪',
            'description' => 'Kelola toko dan produk',
            'dashboard_route' => 'seller.dashboard',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mode Switching Behavior
    |--------------------------------------------------------------------------
    */

    'remember_preference' => true, // Save last mode to DB
    'auto_initialize_on_login' => true, // Set mode automatically on login
];
