<?php
/**
 *
 * @auther heyu 2020/7/24
 */

namespace App\Library;

use Symfony\Component\Console\Output\ConsoleOutput;
use WptCommon\Library\Facades\MLogger;

class Logger
{
    public static function info($message, $content = [])
    {
        if (strtolower(php_sapi_name()) == 'cli') {
            app(ConsoleOutput::class)->writeln(date('Y-m-d H:i:s') . ' ' . $message);
        }
        return MLogger::info('message_center', $message, $content);
    }

    /**
     * 记录错误日志
     * @param string $message
     * @param mixed $content
     * @author heyu  2020/7/29 17:25
     */
    public static function error($message, $content)
    {
        MLogger::error('message_center_error', $message, $content);
        Ding::wechatGroup('error', $message . "\n" . json_encode($content));
    }
}
