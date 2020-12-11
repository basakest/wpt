<?php


namespace WptUtils;

use ArrayAccess;

class Arr
{
    /**
     * @param $value
     * @return bool
     */
    public static function accessible($value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * 数组随机 老方法 arrayRandom
     * @param array $array 需要随机的数组
     * @param int $num 随机几个
     * @param bool $needKey 是否返回随机key
     * @return array|mixed
     */
    public static function rand(array $array, int $num = 1, bool $needKey = false)
    {
        if (empty($array)) {
            return [];
        }
        if ($needKey) {
            return array_rand($array, $num);
        }
        shuffle($array);
        $r = [];
        for ($i = 0; $i < $num; $i++) {
            if (isset($array[$i])) {
                $r[] = $array[$i];
            }
        }
        return $r;
    }

    /**
     * 二维数组排序
     *
     * @param $arr
     * @param $key
     * @param string $type
     * @param int $limit
     * @param null $returnKey
     * @param null $defaultKey
     * @return array
     */
    public static function sort($arr, $key, $type = 'desc', $limit = 0, $returnKey = null, $defaultKey = null)
    {
        $keysvalue = $newArr = [];
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = static::getProperty($v, $key);
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);

        $i = 0;
        foreach ($keysvalue as $k => $v) {
            if ($returnKey == null) {
                $newArr[] = $arr[$k];
            } elseif ($defaultKey) {
                $newArr[$k] = $arr[$k];
            } else {
                $newArr[] = static::getProperty($arr[$k], $returnKey);
            }
            if ($limit > 0) {
                if (++$i >= $limit) {
                    break;
                }
            }
        }
        return $newArr;
    }

    /**
     * 二维数组根据key 排序 老方法 arrayMSort
     * 请勿将外部参数直接传入以防止命令注入！！！
     * @param $array
     * @param $cols
     * @return array
     * @author 丁圆圆
     */
    public static function multiSort($array, $cols)
    {
        $colArr = [];
        foreach ($cols as $col => $order) {
            $colArr[$col] = [];
            foreach ($array as $k => $row) {
                $colArr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colArr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';

        eval($eval);
        $ret = [];
        foreach ($colArr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k])) {
                    $ret[$k] = $array[$k];
                }
                $ret[$k][$col] = $array[$k][$col];
            }
        }

        $output = [];

        foreach ($ret as $item) {
            $output[] = $item;
        }

        return $output;
    }

    /**
     * 数组转对象
     *
     * @param $arr
     * @return object
     */
    public static function toObject($arr)
    {
        if (is_array($arr)) {
            return json_decode(json_encode($arr));
        } else {
            return $arr;
        }
    }

    /**
     * @param $obj
     * @param $property
     * @param null $default
     * @return mixed
     */
    private static function getProperty($obj, $property, $default = null)
    {
        if (!$obj) {
            return $default;
        }
        is_string($obj) and $obj = json_decode($obj, true);
        if (is_object($obj)) {

            return property_exists($obj, $property) || isset($obj->$property) ? $obj->$property : $default;
        }

        return isset($obj[$property]) ? $obj[$property] : $default;
    }
}
