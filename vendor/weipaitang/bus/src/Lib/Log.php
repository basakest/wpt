<?php


namespace WptBus\Lib;


use WptCommon\Library\Tools\Logger;

/**
 * Class Log
 * @package WptBus\Kernel\Logger
 * @method static void error(string $serviceName, string $message, array $context = [])
 * @method static void warning(string $serviceName, string $message, array $context = [])
 * @method static void info(string $serviceName, string $message, array $context = [])
 * @method static void debug(string $serviceName, string $message, array $context = [])
 */
class Log
{
    protected static $instance;

    public static function getInstance()
    {
        $config = ["mlogger" =>
            [
                "logs_dir" => "newlogs/micro-sdk",
                "expand_fields" => ["serviceName", "traceId", 'unique_id'],
                "log_level" => "info"
            ]
        ];
        if (!isset(self::$instance)) {
            self::$instance = new Logger($config);
        }
        return self::$instance;
    }

    public static function __callStatic($method, $arguments)
    {
        $logger = static::getInstance();
        $arguments[0] = "bus-sdk-{$arguments[0]}";
        
        $traceId = Utils::getTraceId();
        if ($traceId) {
            $arguments[2]['traceId'] = $traceId;
        }
        $bizId = Utils::getBizId();
        if ($bizId) {
            $arguments[2]['bizId'] = $bizId;
        }
       
        $arguments[] = true;

        $logger->{$method}(...$arguments);
    }
}