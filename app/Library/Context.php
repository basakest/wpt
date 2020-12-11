<?php


namespace App\Library;

use Illuminate\Support\Arr;

class Context
{
    /**
     * 附加信息
     *
     * @var array
     */
    protected static $attachments = [];

    /**
     * 获得附加信息
     *
     * @return array
     */
    public static function getAttachments()
    {
        return self::$attachments;
    }

    /**
     * 设置附加信息
     *
     * @param array $attachments
     */
    public static function setAttachments(array $attachments)
    {
        self::$attachments = $attachments;
    }

    /**
     * 获得附加信息
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function getAttachment($key, $default = null)
    {
        return Arr::get(self::$attachments, $key, $default);
    }

    /**
     * 设置附加信息
     *
     * @param string $key
     * @param mixed $value
     */
    public static function setAttachment($key, $value)
    {
        self::$attachments[$key] = $value;
    }
}