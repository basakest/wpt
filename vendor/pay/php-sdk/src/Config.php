<?php

namespace PayCenter;

use PayCenter\Exception\ConfigException;

final class Config
{
    private static $host;
    private static $product;
    private static $key;

    const VERSION = '1.1';
    const ENV_KEYS = [
        'host' => 'PAYCENTER_HOST',
        'product' => 'PAYCENTER_PRODUCT',
        'key' => 'PAYCENTER_KEY',
    ];

    /**
     * 设置指定的配置
     * @param int $product
     * @param string $key
     * @param string|null $host
     * @return void
     */
    public static function set(int $product, string $key, string $host = null)
    {
        self::setProduct($product);
        self::setKey($key);
        $host && self::setHost($host);
    }

    /**
     * 恢复默认配置
     * @return void
     */
    public static function restore()
    {
        self::$product = self::$key = self::$host = null;
    }

    /**
     * @return string
     * @throws ConfigException
     */
    public static function getHost(): string
    {
        if (self::$host === null) {
            self::setHost(self::getEnv('host'));
        }

        return self::$host;
    }

    /**
     * @param string $host
     */
    public static function setHost(string $host)
    {
        if (substr($host, -1) != '/') {
            $host .= '/';
        }

        self::$host = $host;
    }

    /**
     * @return int
     * @throws ConfigException
     */
    public static function getProduct(): int
    {
        if (self::$product === null) {
            self::setProduct(self::getEnv('product', 'int'));
        }
        return self::$product;
    }

    /**
     * @param int $product
     */
    public static function setProduct(int $product)
    {
        self::$product = $product;
    }

    /**
     * @return string
     * @throws ConfigException
     */
    public static function getKey(): string
    {
        if (self::$key === null) {
            self::setKey(self::getEnv('key'));
        }

        return self::$key;
    }

    /**
     * @param string $key
     */
    public static function setKey(string $key)
    {
        self::$key = $key;
    }

    /**
     * 读取环境变量配置
     * @param string $item
     * @param string $type
     * @return mixed
     * @throws ConfigException
     */
    private static function getEnv(string $item, string $type = 'string')
    {
        $value = getenv($envKey = self::ENV_KEYS[$item]);
        if ($value === false) {
            throw new ConfigException('支付中心配置项不存在：' . $envKey);
        }

        if (!settype($value, $type)) {
            throw new ConfigException('支付配置参数类型不正确[' . $type . ']：' . $envKey);
        }

        return $value;
    }
}
