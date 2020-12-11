<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'host' => env('AUTH_HOST', "http://authapit.weipaitang.com"),
        'platform' => env('AUTH_PLATFORM', 'marketing'),
        'timeout' => 5
    ],

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */
    'login' => [
        'host' => env('USER_HOST', "https://login.weipaitang.com"),
        'domain' => env('USER_DOMAIN', "middle-admin.weipaitang.com"),
        'timeout' => 5
    ],

];
