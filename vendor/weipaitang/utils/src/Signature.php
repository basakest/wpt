<?php


namespace WptUtils;

class Signature
{
    /**
     * 用户加密方式可能会改  老方法 CommonUtil::userSign()
     * @param $str
     * @return string
     */
    public static function userSignature($str)
    {
        return md5($str);
    }
}
