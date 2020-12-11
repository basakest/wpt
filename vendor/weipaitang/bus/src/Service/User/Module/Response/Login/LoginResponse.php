<?php

namespace WptBus\Service\User\Module\Response\Login;

use WptBus\Service\User\Module\Response\BaseResponse;

class LoginResponse extends BaseResponse
{
    public $userinfoId;

    public $uri;

    public $openid;

    public $isNew;

    public $isFirstRegister;

    /** @var LoginResponsePlatformInfo */
    public $platformInfo;
}

class LoginResponsePlatformInfo
{
    public $platformId;

    public $originalUid;

    public $originalUri;

    public $originalOpenid;
}