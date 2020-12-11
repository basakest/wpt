<?php

namespace App\Utils;

/**
 * 用作单例
 *
 * Trait Singleton
 * @package App\Utils
 */
trait Singleton
{
    private static $instance;

    /**
     * @return static
     */
    public static function getInstance(...$args)
    {
        if (!isset(self::$instance)) {
            self::$instance = new static(...$args);
        }
        return self::$instance;
    }
}
