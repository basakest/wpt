<?php

namespace WptUtils;

/**
 * 字符类相关操作
 *
 * Class Strings
 * @package WptUtils
 */
class Str
{
    /**
     * 所有字符正则 (头)($1)(尾)
     */
    const ALLCHARSET_REGEX = '/([a-zA-Z0-9\x{4e00}-\x{9fbf}])(.*)([a-zA-Z0-9\x{4e00}-\x{9fbf}])/u';

    /**
     * 一些特殊符号
     *
     * @var array
     */
    private static $specialSymbol = [
        '`', '·', '~', '!', '！', '@', '#', '$', '￥', '%', '^', '……', '&', '*', '(', ')', '（',
        '）', '-', '_', '——', '+', '=', '|', '\\', '[', ']', '【', '】', '{', '}', ';', '；', ':',
        '：', '\'', '"', '“', '”', ',', '，', '<', '>', '《', '》', '.', '。', '/', '、', '?', '？'
    ];

    /**
     * 将字符中的换行符转为html转义字符
     * 对应老方法formatDesc
     *
     * @param $chatset
     * @return string|string[]
     */
    public static function convertLineBreak($chatset)
    {
        if (empty($chatset)) {
            return '';
        }
        $search = [chr(32), "\n"];
        $replace = ['&nbsp;', '<br />'];
        return str_replace($search, $replace, $chatset);
    }

    /**
     * 截取UTF-8编码下字符串的函数
     * 对应老方法 subStr
     *
     * @param $str
     * @param int $length
     * @param bool $append
     * @return false|string|string[]
     */
    public static function substr($str, $length = 0, $append = true)
    {
        $str = str_replace("\n", '', str_replace("\r", '', trim($str)));
        $strlength = strlen($str);

        if ($length == 0 || $length >= $strlength) {
            return $str;
        } elseif ($length < 0) {
            $length = $strlength + $length;
            if ($length < 0) {
                $length = $strlength;
            }
        }

        if (function_exists('mb_substr')) {
            $newstr = mb_substr($str, 0, $length, 'utf-8');
        } elseif (function_exists('iconv_substr')) {
            $newstr = iconv_substr($str, 0, $length, 'utf-8');
        } else {
            $newstr = substr($str, 0, $length);
        }

        if ($append && $str != $newstr) {
            $newstr .= '...';
        }

        return $newstr;
    }

    /**
     * json 过滤 把会导致json_decode失败的特殊字符替换成空，例子：长乐�无求
     *
     * @param $data
     * @param bool $br
     * @return mixed
     */
    public static function filterJSON($data, $br = false)
    {
        if ($br) {
            $data = preg_replace(
                '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
                '|[\x00-\x7F][\x80-\xBF]+' .
                '|\n+' .
                '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
                '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
                '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
                '',
                $data
            );
        } else {
            $data = preg_replace(
                '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
                '|[\x00-\x7F][\x80-\xBF]+' .
                '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
                '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
                '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
                '',
                $data
            );
        }
        $data = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]' .
            '|\xED[\xA0-\xBF][\x80-\xBF]/S', '', $data);

        return $data;
    }


    /**
     * 字符超一定的长度、截取首尾各一个字符，中间*替换
     *
     * @param string $str 需要处理的字符串
     * @param int $len 超过多少字符处理
     * @return string
     */
    public static function getStrTooLong($str, $len)
    {
        if (mb_strlen($str) >= $len) {
            $start = mb_substr($str, 0, 1);
            $end = mb_substr($str, -1, 1);
            $mid = '**';
            return $start . $mid . $end;
        } else {
            return $str;
        }
    }

    /**
     * 过滤emoji表情
     *
     * @param $str
     * @return mixed
     */
    public static function filterEmoji($str)
    {
        $str = preg_replace_callback('/./u', function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $str);
        return $str;
    }

    /**
     * 过滤特殊字符 如：微信nickname
     *
     * @param string $data
     * @return string
     */
    public static function stripSpecialChars($data)
    {
        return str_replace(["'", "\"", "<", ">", "\\", "\n", "%"], [''], strip_tags($data));
    }

    /**
     * 根据uniqid,随机数,机器ip,时间获取唯一值
     *
     * @param mixed $data 一起来进行区分的数据
     * @return string
     */
    public static function uuid($data = [])
    {
        $uuid = uniqid(md5(microtime(true)), true);
        $uuid .= static::randString(12);
        if (!empty($data)) {
            $uuid .= json_encode($data);
        }

        $uuid = md5($uuid);
        $timeStr = str_replace('.', '', microtime(true));
        $uuid .= $timeStr;
        if (!empty($_SERVER['SERVER_ADDR'])) {
            $chars = "abcdefghijklmnopqrstuvwxyz";
            $uuid .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            $serverAddr = ip2long($_SERVER['SERVER_ADDR']);
            $uuid .= $serverAddr;
        } else {
            $uuid .= static::randString(11);
        }
        return $uuid;
    }

    /**
     * 取得随机代码 对应老方法 getRandStr
     *
     * @param int $length
     * @return string
     */
    public static function randString($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 获取URL中的参数
     *
     * @param $url
     * @param string $key
     * @return mixed
     */
    public static function parseUrl($url, $key = '')
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $output);
        if (!empty($key)) {
            return $output[$key];
        }
        return $output;
    }


    /**
     * 添加url参数
     *
     * @param $url
     * @param array $parameter
     * @return false|string
     */
    public static function addUrlParameter($url, array $parameter = [])
    {
        $parameters = static::parseUrl($url);
        foreach ($parameter as $key => $value) {
            $parameters[$key] = $value;
        }
        $query = http_build_query($parameters);
        if (stristr($url, '?')) {
            $new_url = substr($url, 0, strrpos($url, '?'));
        } else {
            $new_url = $url;
        }
        if (empty($query)) {
            return $new_url;
        }
        return $new_url . '?' . $query;
    }

    /**
     * 昵称转换***
     *
     * @param string $string 昵称
     * @param int $starNum 星号数量
     * @return string
     */
    public static function protectNickname($string, $starNum = 3)
    {
        $len = mb_strlen($string);
        if ($len == 2) {
            return mb_substr($string, 0, 1, 'utf-8') . '***' . mb_substr($string, 1, 1, 'utf-8');
        }

        $string = static::removeSpecialSymbol($string);
        return preg_replace(static::ALLCHARSET_REGEX, '$1' . str_repeat('*', $starNum) . '$3', $string);
    }

    /**
     * 过滤代码 原方法strFilter
     * @param $str
     * @return string
     */
    public static function removeSpecialSymbol($str)
    {
        return trim(str_replace(static::$specialSymbol, '', $str));
    }

    /**
     * @param $subject
     * @param $search
     * @return mixed
     */
    public static function after($subject, $search)
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }

    /**
     * @param $subject
     * @param $search
     * @return mixed
     */
    public static function before($subject, $search)
    {
        return $search === '' ? $subject : explode($search, $subject)[0];
    }

    /**
     * 是否包含自定字符
     *
     * @param string $haystack
     * @param array|string $needles
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}
