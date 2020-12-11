<?php
/**
 *
 * @auther heyu 2020/7/10
 */

namespace App\Library;

class Util
{

    /**
     * 计算倍率
     * @param double $dividend 被除数
     * @param double $divisor 除数
     * @return string
     * @author heyu  2020/7/24 11:06
     */
    public static function getRate($dividend, $divisor)
    {
        if ($divisor == 0) {
            return '0%';
        }
        return round($dividend / $divisor * 100, 2) . '%';
    }

    public static function camelize($str)
    {
        return ltrim(str_replace(" ", "", ucwords('_' . str_replace('_', " ", strtolower($str)))), '_');
    }

    public static function unCamelize($str)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . '_' . "$2", $str));
    }

    /**
     * 数组key 驼峰化
     * @param $array
     * @return array
     * @author heyu  2020/7/10 13:58
     */
    public static function camelizeArrayKey($array)
    {
        $return = [];
        foreach ($array as $k => $v) {
            $return[self::camelize($k)] = $v;
        }
        return $return;
    }

    /**
     * 数组key 去驼峰化
     * @param $array
     * @return array
     * @author heyu  2020/7/10 13:58
     */
    public static function unCamelizeArrayKey($array)
    {
        $return = [];
        foreach ($array as $k => $v) {
            $return[self::unCamelize($k)] = $v;
        }
        return $return;
    }

    public static function encodeNumber($number)
    {
        $str = '';
        $dic = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $len = strlen($dic);

        $maxLen = 5;
        $height = 11;
        $max = pow($len, $maxLen) - 1;
        $width = intval($max / $height);
        $maxX = $max % $width;
        $maxY = intval($max / $width);

        $m = ($maxY + 1) * ($maxX + 1);
        if ($number < $m) {
            $x = intval($number / ($maxY + 1));
            $y = $number % ($maxY + 1);
        } elseif ($number <= $max) {
            $x = intval(($number - $m) / ($maxY)) + 1 + $maxX;
            $y = intval(($number - $m) % ($maxY));
        } else {
            throw new \Exception('超出最大限制');
        }
        $number = $y * $width + $x;

        while (true) {
            $str = $dic{$number % $len} . $str;
            $number = $number / $len;
            if ($number < 1) {
                break;
            }
        }
        return str_pad($str, 5, $dic{0}, STR_PAD_LEFT);
    }

    public static function pre($p)
    {
        echo "<pre>";
        print_r($p);
        die();
    }
}
