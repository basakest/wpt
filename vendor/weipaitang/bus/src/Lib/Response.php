<?php


namespace WptBus\Lib;

class Response
{
    public static function byBus($errorCode, $errorMsg = "")
    {
        if (empty($errorMsg)) {
            $errorMsg = Error::ERROR_MSG[$errorCode] ?? "";
        }
        return ['code' => $errorCode, 'msg' => $errorMsg];
    }

    public static function bySrv($data)
    {
        return ['code' => $data['code'], 'msg' => $data['msg'], 'data' => $data['data'] ?? null, 'nowTime' => $data['nowTime']];
    }
}