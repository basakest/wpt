<?php
namespace App\Utils;

use WptCommon\Library\Facades\MLogger;

class JWTUtil
{
    /**
     * 定义头部
     * @var array
     */
    private static $header = array(
        'alg' => 'HS256',  //生成signature的算法
        'type' => 'JWT'    //类型
    );

    // key
    private static $key = 'e48305057eb9732b';


    /**
     * 获取token
     * [
     *  'iat'=>time(),  //签发时间
     *  'exp'=>time()+7200,  //过期时间
     *  'nbf'=>time()+60,  //该时间之前不接收处理该Token
     *  'user'=>'admin1',  //面向的用户
     *  'jti'=>md5(uniqid('JWT').time())  //该Token唯一标识
     * ]
     * @param array $payload
     * @return string
     */
    public static function getToken(array $payload)
    {
        $base64header = self::base64UrlEncode(json_encode(self::$header, JSON_UNESCAPED_UNICODE));
        $base64payload = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_UNICODE));
        $token = $base64header . '.' . $base64payload . '.' .
            self::signature($base64header . '.' . $base64payload, self::$key, self::$header['alg']);
        return $token;
    }

    /**
     * 验证token
     * @param string $token
     * @return bool|mixed
     */
    public static function verifyToken(string $token)
    {
        $tokens = explode('.', $token);
        if (count($tokens) != 3) {
            MLogger::warning('jwtUtil', 'token非法', ['token' => $token]);
            return false;
        }

        list($base64header, $base64payload, $sign) = $tokens;

        //获取jwt算法
        $base64DecodeHeader = json_decode(self::base64UrlDecode($base64header), JSON_OBJECT_AS_ARRAY);
        if (empty($base64DecodeHeader['alg'])) {
            MLogger::warning('jwtUtil', 'token非法', ['token' => $token, 'base64DecodeHeader' => $base64DecodeHeader]);
            return false;
        }

        //签名验证
        if (self::signature($base64header . '.' . $base64payload, self::$key, $base64DecodeHeader['alg']) !== $sign) {
            MLogger::warning('jwtUtil', '签名验证失败', ['token' => $token, 'sign' => $sign]);
            return false;
        }

        $payload = json_decode(self::base64UrlDecode($base64payload), JSON_OBJECT_AS_ARRAY);

        //签发时间大于当前服务器时间验证失败
        if (isset($payload['iat']) && $payload['iat'] > time()) {
            MLogger::warning('jwtUtil', '签发时间大于服务器时间', ['token' => $token, 'sign' => $sign]);
            return false;
        }

        //过期时间小宇当前服务器时间验证失败
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            MLogger::warning('jwtUtil', '过期时间小于服务器时间', ['token' => $token, 'sign' => $sign]);
            return false;
        }

        //该nbf时间之前不接收处理该Token
        if (isset($payload['nbf']) && $payload['nbf'] > time()) {
            MLogger::warning('jwtUtil', 'nbf时间之前不接收处理', ['token' => $token, 'sign' => $sign]);
            return false;
        }

        return $payload;
    }

    /**
     * base64加密
     * @param string $input
     * @return string|string[]
     */
    private static function base64UrlEncode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * base64解密
     * @param string $input
     * @return false|string
     */
    private static function base64UrlDecode(string $input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $addlen = 4 - $remainder;
            $input .= str_repeat('=', $addlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * 签名
     * @param string $input
     * @param string $key
     * @param string $alg
     * @return string|string[]
     */
    public static function signature(string $input, string $key, string $alg = 'HS256')
    {
        $config = ['HS256' => 'sha256'];
        return self::base64UrlEncode(hash_hmac($config[$alg], $input, $key, true));
    }
}
