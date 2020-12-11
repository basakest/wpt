<?php

namespace WptBus\Config;

return [
    'http' => [
        'name' => 'shop',
        'servers' => [],
        'balance' => 'mainSpare', // 主备
        'connectTimeout' => 200, // 连接超时ms
        'readTimeout' => 2000, // 读超时ms
        'debug' => false, // 请求日志记录返回结果
    ]
];