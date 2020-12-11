<?php


namespace WptBus\Service\User\Module\Request\Login;


use WptBus\Service\User\Module\Request\BaseRequest;

class LoginByWechatCx extends BaseRequest
{
    public $code;

    public $iv;

    public $encryptedData;

    public $rawData;

    public $signature;

    public $deviceId;

    public $sysMessageNum;
}