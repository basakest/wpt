<?php


namespace WptOrder\OrderService\Tools;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use WptOrder\OrderService\Exceptions\InvalidConfigException;

/**
 * @see Logger
 * @method void info(string $message, array $context = [])
 * Class Log
 * @package WptOrder\OrderService\Tools
 */
class Log
{
    const TYPE_DAILY = 'daily';
    const TYPE_HOURLY = 'hourly';

    const types = [
        self::TYPE_DAILY, self::TYPE_HOURLY
    ];

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Log constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->createLogger($config);
    }

    /**
     * @param array $config
     * @throws \Exception
     */
    protected function createLogger(array $config = [])
    {
        $type = !empty($config['type']) ? $config['type'] : static::TYPE_DAILY;
        $file = $config['file'] ?? "";

        if (!in_array($type, self::types) || empty($file)) {
            throw new InvalidConfigException("日志配置错误");
        }
        $file = $this->resolveFileName($file, $type);
        $logger = new Logger("orderService");
        $logger->pushHandler(new StreamHandler($file, Logger::DEBUG));
        $this->logger = $logger;
    }


    protected function resolveFileName(string $file, string $type)
    {
        $pathInfo = pathinfo($file);
        $dir = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];
        $pathExtra = $type == self::TYPE_DAILY ? date("Y-m-d") : date('Y-m-d-H');

        return "{$dir}/{$filename}-{$pathExtra}.{$extension}";
    }

    public function __call($name, $arguments)
    {
        return $this->logger->{$name}(...$arguments);
    }

}