<?php

return [
    'api' => 'saleGo',
    'log' => [
        'type' => 'daily', // 日志类型：daily，hourly
        'file' => __DIR__ . "/../logs/order-service.log",// 日志文件名
    ]
];