<?php


namespace WptBus\Lib;


class Error
{
    const INVALID_ARGUMENT = -10001;
    const INVALID_CONFIG = -10002;
    const TRANSPORT = -10003;
    const RETURN_FORMAT_ERROR = -10004;
    const SYSTEM_EXCEPTION = -10005;

    const ERROR_MSG = [
        self::INVALID_ARGUMENT => "不合法的参数",
        self::INVALID_CONFIG => "不合法的配置",
        self::TRANSPORT => "服务请求失败",
        self::RETURN_FORMAT_ERROR => "服务返回格式错误",
        self::SYSTEM_EXCEPTION => "服务系统异常",
    ];

    public static function getBusErrorMsgInfo($code, $msg)
    {
        if (!Utils::isEnvTest() || empty($msg) || !is_string($msg)) {
            return "";
        }
        switch ($code) {
            case self::TRANSPORT:
                $msgArr = json_decode($msg, true);
                if (is_array($msgArr)) {
                    $showField = ["shortError", "url", "body", "result", "uniqueId", "curlError"];
                    $showMsgArr = array_intersect_key($msgArr, array_flip($showField));
                    return json_encode($showMsgArr);
                }
                return (string)$msg;
                break;
            default:
                return $msg;
        }
    }
}