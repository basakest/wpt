<?php

namespace WptUtils\Logger;

use WptCommon\Library\Tools\Logger as MLogger;

/**
 * Class Log
 * @package WptDataCenter\Log
 *
 * @method static bool error($filePrefix, string $message, $content = [], $isExpand = false, $alertType = '')
 * @method static bool info($filePrefix, string $message, $content = [], $isExpand = false, $alertType = '')
 * @method static bool warning($filePrefix, string $message, $content = [], $isExpand = false, $alertType = '')
 * @method static bool debug($filePrefix, string $message, $content = [], $isExpand = false, $alertType = '')
 */
class Logger
{
    /**
     * @var $instance Logger
     */
    protected static $instance;

    /**
     * @return MLogger
     */
    public static function getInstance()
    {
        $config = ["mlogger" =>
            [
                "logs_dir" => "newlogs",
                'expand_fields' => ['id', 'saleid', 'uri', 'saleuri', 'createtime', 'endtime', 'opentime', 'type',
                    'status', 'category', 'seccategory', 'price', 'userinfoid', 'userinfouri', 'winuserinfoid',
                    'bail', 'balance', 'money', 'info', 'result', 'origin', 'code', 'errorcode', 'roomid', 'roomuri',
                    'sc', 'orderno', 'outtradeno', 'totalfee', 'from', 'fromuri', 'fromid', 'number', 'uip', 'date',
                    'time', 'tel', 'extend1', 'extend2', 'extend3'],
                "log_level" => "info"
            ]
        ];
        if (!isset(self::$instance)) {
            self::$instance = new MLogger($config);
        }
        return self::$instance;
    }

    /**
     * @param $method
     * @param $arguments
     */
    public static function __callStatic($method, $arguments)
    {
        $logger = static::getInstance();
        $arguments[] = '';
        $logger->{$method}(...$arguments);
    }
}
