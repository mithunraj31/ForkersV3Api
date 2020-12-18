<?php

return [
    'hostname' => env('STONKAM_HOSTNAME'),
    'stk_user' => env('DEFAULT_STK_USER'),
    'auth' => [
        'admin' => [
            'username' => env('STONKAM_AUTH_ADMIN_USERNAME'),
            'password' => env('STONKAM_AUTH_ADMIN_PASSWORD'),
            'version' => env('STONKAM_AUTH_ADMIN_VERSION'),
            'authtype' => env('STONKAM_AUTH_ADMIN_AUTHTYPE')
        ],
        'customer' => [
            'password' => env('STONKAM_CUSTOMER_PASSWORD')
        ]
    ],
    'make_video_time_limit' => env('STONKAM_MAKE_VIDEO_TIME_LIMIE_MINUTE'),
];
