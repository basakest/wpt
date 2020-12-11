<?php

return [
    'mlogger' => [
        /**
         * 日志级别
         */
        'log_level' => env('APP_LOG_LEVEL', 'info'),

        /**
         * 日志文件目录
         */
        'logs_dir' => 'newlogs',

        /**
         * 展开字段
         */
        'expand_fields' => [
            'id', 'uri', 'createtime', 'type', 'status', 'category',
            'price', 'userinfoid', 'userinfouri', 'info', 'result',
            'origin', 'code', 'errorcode'
        ]
    ]
];