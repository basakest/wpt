<?php

namespace WptCommon\Library\Tools;

use Illuminate\Support\Str;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use WptCommon\Library\Consts\AlertTypes;
use WptCommon\Library\Consts\LogTypes;

class Logger
{
    /**
     * 配置信息
     * @var array
     */
    private $config = [];

    /**
     * 日志级别
     * @var int|string
     */
    private $logLevel;

    /**
     * 实例列表
     * @var array
     */
    private $loggers = [];

    /**
     * 日志文件目录
     * @var string
     */
    private $logsDir = 'logs';

    /**
     * 展开字段
     * @var array
     */
    private $expandFields = [];

    private static $instance;

    public function __construct(array $config = [])
    {
        $this->config = $this->mergeConfig($config)['mlogger'];
        $this->logsDir = $this->config['logs_dir'];
        $this->expandFields = $this->config['expand_fields'];
        $this->logLevel = $this->config['log_level'];
    }

    public static function getInstance(array $config = [])
    {
        if (!isset(self::$instance)) {
            self::$instance = new static($config);
        }
        return self::$instance;
    }

    /**
     * 配置合并
     * @param array $config
     * @return array
     */
    protected function mergeConfig(array $config)
    {
        static $default;
        if (!isset($default)) {
            $default = include __DIR__ . "/../../config/common-library.php";
        }

        return array_merge($default, $config);
    }

    /**
     * @param string|array $filePrefix 文件名前缀
     * @param string $message 日志信息
     * @param array $content 日志内容
     * @return void
     */
    public function debug($filePrefix, string $message, $content = [])
    {
        return $this->write($filePrefix, MonologLogger::DEBUG, $message, $content);
    }

    /**
     * @param string|array $filePrefix 文件名前缀
     * @param string $message 日志信息
     * @param array $content 日志内容
     * @param bool $isExpand 是否在es展开特定字段
     * @param string|array $alertType 报警渠道，默认为空(不报警)，多渠道可传数组
     * @return void
     */
    public function info($filePrefix, string $message, $content = [], $isExpand = false, $alertType = '')
    {
        return $this->write($filePrefix, MonologLogger::INFO, $message, $content, $isExpand, $alertType);
    }


    /**
     * @param string|array $filePrefix 文件名前缀
     * @param string $message 日志信息
     * @param array $content 日志内容
     * @param bool $isExpand 是否在es展开特定字段
     * @param string|array $alertType 报警渠道，默认为空(不报警)，多渠道可传数组
     * @return void
     */
    public function warning($filePrefix, string $message, $content = [], $isExpand = false, $alertType = '')
    {
        return $this->write($filePrefix, MonologLogger::WARNING, $message, $content, $isExpand, $alertType);
    }

    /**
     * @param string|array $filePrefix 文件名前缀
     * @param string $message 日志信息
     * @param array $content 日志内容
     * @param bool $isExpand 是否在es展开特定字段
     * @param array $alertType 报警渠道，默认钉钉、邮件报警
     * @return void
     */
    public function error(
        $filePrefix,
        string $message,
        $content = [],
        $isExpand = false,
        $alertType = [AlertTypes::ALERT_DING, AlertTypes::ALERT_MAIL]
    ) {
        return $this->write($filePrefix, MonologLogger::ERROR, $message, $content, $isExpand, $alertType);
    }

    /**
     * 记录异常信息
     * @param string|array $filePrefix 文件名前缀
     * @param \Throwable $e 异常
     * @param string $message 额外日志信息
     * @param array $alertType 报警渠道，默认钉钉、邮件报警
     * @return void
     */
    public function exception($filePrefix, \Throwable $e, $message = '', $alertType = [AlertTypes::ALERT_DING, AlertTypes::ALERT_MAIL])
    {
        $content = [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'detail' => $e->getTraceAsString(),
            'file' => $e->getFile(),
        ];
        return $this->write($filePrefix, MonologLogger::CRITICAL, $message ?: $e->getMessage(), $content, false, $alertType);
    }


    private function getLogger($filePrefix = 'lumen')
    {
        $fileName = $this->getFileName($filePrefix);

        if (isset($this->loggers[$fileName]) && $this->loggers[$fileName] instanceof MonologLogger) {
            return $this->loggers[$fileName];
        }

        $logger = new MonologLogger(env('APP_ENV', 'default'));
        $logger->pushHandler((new StreamHandler($this->getFilePath($fileName), $this->logLevel))->setFormatter($this->getFormatter()));
        $this->loggers[$fileName] = $logger;
        return $logger;
    }

    private function getMicroDateTime()
    {
        $uTimestamp = microtime(true);
        $milliseconds = sprintf('%06d', round(($uTimestamp - floor($uTimestamp)) * 1000000));
        return date("Y-m-d\TH:i:s.{$milliseconds}P");
    }

    /**
     * @param $filePrefix
     * @param $level
     * @param string $message
     * @param array $content
     * @param bool $isExpand
     * @param string $alertType
     * @return
     */
    private function write($filePrefix, $level, string $message, $content = [], $isExpand = false, $alertType = '')
    {
        $alertType = is_array($alertType) ? implode('|', $alertType) : $alertType;
        AlertTypes::check($alertType);
        $log = [
            '@timestamp' => $this->getMicroDateTime(),
            'level' => Str::lower(MonologLogger::getLevelName($level)),
            'message' => $message,
            'content' => is_array($content) || is_object($content) ? json_encode($content, JSON_UNESCAPED_UNICODE) : $content,
            'trace_id' => defined('TRACE_ID') ? TRACE_ID : '',
            'url' => php_sapi_name() == 'cli' ? $this->getCommand() : $this->getUrl(),
            'param' => json_encode($this->getParam(), JSON_UNESCAPED_UNICODE),
            'alert' => is_array($alertType) ? implode('|', $alertType) : $alertType
        ];

        if ($isExpand && is_array($content)) {
            $log['expand'] = $this->expand($content);
        }

        $uniqueId = $this->getProperty($content, 'unique_id', '');
        if (!empty($uniqueId)) {
            $log['unique_id'] = $uniqueId;
        }

        return $this->getLogger($filePrefix)->log($level, '', $log);
    }

    private function getProperty($obj, $key, $default)
    {
        if (is_array($obj)) {
            return $obj[$key] ?? $default;
        } elseif (is_object($obj)) {
            return $obj->$key ?? $default;
        } else {
            return $default;
        }
    }

    private function getUrl()
    {
        if (function_exists('app')) {
            return app('request')->url();
        }
        return sprintf("%s://%s%s", $_SERVER['REQUEST_SCHEME'] ?? 'http', $_SERVER['HTTP_HOST'] ?? '', $_SERVER['PATH_INFO'] ?? '');
    }

    private function getCommand()
    {
        if (function_exists('app')) {
            return app('request')->server->get('_', '') . ' ' . implode(' ', app('request')->server->get('argv', []));
        }
        return sprintf("%s %s", $_SERVER['_'] ?? '', implode(' ', $_SERVER['argv'] ?? []));
    }

    private function getParam()
    {
        if (function_exists('app')) {
            return app('request')->input();
        }
        return ($_GET ?? []) + ($_POST ?? []);
    }

    /**
     * 内容展开
     * 除了price字段，其他字段为数组或对象将被过滤
     * @param $content
     * @return array
     */
    private function expand($content)
    {
        return array_filter($content, function ($value, $key) {
            if (is_array($value) || is_object($value)) {
                return $key == 'price';
            }
            return in_array(strtolower($key), $this->expandFields);
        }, 1);
    }

    /**
     * 获取文件路径
     * @param string $fileName
     * @return string
     */
    private function getFilePath($fileName)
    {
        $basePath = $this->getBasePath();
        $storagePath = stripos($basePath, "modules") ? $basePath . '/../storage' : $basePath . '/storage';
        return $storagePath . '/' . $this->logsDir . '/' . $fileName . '.log';
    }

    private function getBasePath()
    {
        if (function_exists('base_path')) {
            return base_path();
        }
        return php_sapi_name() == 'cli' ? getcwd() : realpath(getcwd() . '/../');
    }


    /**
     * 获取文件名
     * @param string|array $prefix 文件前缀。若为数组，第一个值为文件名时间类型，目前支持hourly/daily；第二个值为文件前缀
     * @return string
     */
    private function getFileName($prefix)
    {
        list($type, $prefix) = is_array($prefix) ? $prefix : [LogTypes::LOG_TYPE_DAILY, $prefix];
        $suffix = in_array($type, [LogTypes::LOG_TYPE_HOURLY, LogTypes::LOG_TYPE_HOUR]) ? date('Y-m-d-H') : date('Y-m-d');
        return $prefix . '-' . $suffix;
    }

    /**
     * 设置日志格式
     * @return LineFormatter
     */
    private function getFormatter()
    {
        return new LineFormatter("%context%\n", null, false);
    }


}
