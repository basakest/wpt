<?php
/**
 *
 * @auther heyu 2020/6/30
 */

return [
    'default' => env('DB_CONNECTION', 'default'),

    'connections' => [
        'default' => [
            'host' => env('MASTER_DB_HOST'),
            'port' => env('MASTER_DB_PORT'),
            'database' => env('MASTER_DB_NAME'),
            'username' => env('MASTER_DB_USERNAME'),
            'password' => env('MASTER_DB_PASSWORD'),
            'driver' => 'mysql',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'prefix' => '',
        ],
        'main' => [
            'host' => env('MASTER_DB_HOST'),
            'port' => env('MASTER_DB_PORT'),
            'database' => env('MASTER_DB_NAME'),
            'username' => env('MASTER_DB_USERNAME'),
            'password' => env('MASTER_DB_PASSWORD'),
            'driver' => 'mysql',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'prefix' => '',
        ],
    ],


    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ]
    ],
];
