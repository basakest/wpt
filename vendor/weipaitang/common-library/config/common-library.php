<?php

return [
    'mlogger' => [
        /**
         * 日志级别
         */
        'log_level' => 'info',

        /**
         * 日志文件目录
         */
        'logs_dir' => 'newlogs',

        /**
         * 展开字段
         */
        'expand_fields' => ['id', 'saleid', 'uri', 'saleuri', 'createtime', 'endtime', 'opentime', 'type',
            'status', 'category', 'seccategory', 'price', 'userinfoid', 'userinfouri', 'winuserinfoid',
            'bail', 'balance', 'money', 'info', 'result', 'origin', 'code', 'errorcode', 'roomid', 'roomuri',
            'sc', 'orderno', 'outtradeno', 'totalfee', 'from', 'fromuri', 'fromid', 'number', 'uip', 'date',
            'time', 'tel', 'extend1', 'extend2', 'extend3']
    ]
];