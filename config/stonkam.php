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
        ]
    ]
];
