<?php


namespace WptCommon\Library\Facades;

use Illuminate\Support\Facades\Facade;
use WptCommon\Library\Consts\AlertTypes;

/**
 * Class MLogger
 * @method static bool debug($filePrefix, string $message, $content = [])
 * @method static bool info($filePrefix, string $message, $content = [], $isExpand = false, $alertType = '')
 * @method static bool warning($filePrefix, string $message, $content = [], $isExpand = false, $alertType = '')
 * @method static bool error($filePrefix, string $message, $content = [], $isExpand = false, $alertType = [AlertTypes::ALERT_DING, AlertTypes::ALERT_MAIL])
 * @method static exception($filePrefix, \Throwable $e, $message = '', $alertType = [AlertTypes::ALERT_DING, AlertTypes::ALERT_MAIL])
 *
 * @package WptCommon\Library\Facades\
 */
class MLogger extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'mlogger';
    }
}