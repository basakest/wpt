<?php

namespace PayCenter;

final class Signature
{
    const SIGNATURE_KEY = 'signature';

    /**
     * 生成签名
     * @param array $data
     * @param string $key
     * @return string
     */
    public static function generate(array $data, string $key): string
    {
        //签名步骤一：按sudo 字典序排序参数
        ksort($data);

        // 格式化参数格式化成url参数
        $buff = "";
        foreach ($data as $k => $v) {
            if ($k != self::SIGNATURE_KEY && !empty($v) && !is_array($v) && !is_object($v)) { // 添加了"signature" 和 !is_object($v)
                $buff .= $k . "=" . $v . "&";
            }
        }
        $string = trim($buff, "&");

        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        return strtoupper($string);
    }

    /**
     * 验证签名
     * @param array $data
     * @param string $key
     * @param string $signature
     * @return bool
     */
    public static function check(array $data, string $key, string $signature = ""): bool
    {
        return !strcasecmp(self::generate($data, $key), $data[self::SIGNATURE_KEY] ?? $signature);
    }
}
