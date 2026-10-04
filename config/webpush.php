<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Web Push VAPID Settings
    |--------------------------------------------------------------------------
    | Voluntary Application Server Identification (VAPID) keys.
    */
    'vapid' => [
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
        'subject' => env('VAPID_SUBJECT', 'mailto:admin@bcafe.ir'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Guzzle Client Timeout
    |--------------------------------------------------------------------------
    | Outbound HTTP request timeout for push service deliveries.
    */
    'timeout' => 20,

    /*
    |--------------------------------------------------------------------------
    | Automatic Dead Subscription Pruning
    |--------------------------------------------------------------------------
    | Automatically delete subscriptions that respond with 410 Gone or 404.
    */
    'automatic_pruning' => true,
];
