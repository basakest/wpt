<?php


namespace WptUtils;

class Crypto
{
    /**
     * des加密 老方法 CommonUtil::desEncrypt()
     *
     * @param string $data
     * @param string $key
     * @return string
     */
    public static function desCbcEncrypt(string $data, string $key = 'wpt'): string
    {
        return openssl_encrypt($data, "DES-CBC", $key, OPENSSL_RAW_DATA, substr(md5($key), 0, 8));
    }

    /**
     * des解密 老方法 CommonUtil::desDecrypt()
     *
     * @param string $data
     * @param string $key
     * @return string
     */
    public static function desCbcDecrypt(string $data, string $key = 'wpt'): string
    {
        return openssl_decrypt($data, "DES-CBC", $key, OPENSSL_RAW_DATA, substr(md5($key), 0, 8));
    }

    /**
     * suid加密
     * @param $toEncrypt
     * @return string
     */
    public static function suidEncrypt($toEncrypt)
    {
        $encryptKey = '25dec7ffdbaf8e67fa8cbecc2a6f3c75';
        $blockSize = 16;

        $iv = mcrypt_create_iv($blockSize, MCRYPT_RAND);
        return base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $encryptKey, $toEncrypt, MCRYPT_MODE_CBC, $iv));
    }
}
