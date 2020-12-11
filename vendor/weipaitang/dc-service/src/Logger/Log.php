<?php

namespace WptDataCenter\Logger;

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
class Log
{
    /**
     * @var $instance Log
     */
    protected static $instance;

    /**
     * @return MLogger
     */
    public static function getInstance()
    {
        $config = ["mlogger" =>
            [
                "logs_dir" => "newlogs/micro-sdk",
                "expand_fields" => ["httpcode", "path", "curlcode", "unique_id"],
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
        $arguments[] = true;
        $logger->{$method}(...$arguments);
    }
}
