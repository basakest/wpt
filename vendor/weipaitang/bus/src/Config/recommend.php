<?php

namespace WptBus\Config;

return [
    'http' => [
        'name' => 'recommend',
        'servers' => [],
        'balance' => 'mainSpare', // 主备
        'connectTimeout' => 200, // 连接超时ms
        'readTimeout' => 2000, // 读超时ms
        'debug' => false, // 输出请求日志
    ]
];