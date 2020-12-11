<?php

namespace WptBus\Service\User\Module\Request;

class SavePersonalAuthenticationInfoRequest extends BaseRequest
{
    /**
     * @var int
     */
    public $uid = 0;
    /**
     * @var string
     * 店铺名
     */
    public $shopName = "";
    /**
     * @var string
     * 店铺logo
     */
    public $shopLogo = "";
    /**
     * @var string
     * 店铺简介
     */
    public $shopIntroduce = "";
    /**
     * @var string
     * 认证人姓名
     */
    public $name = "";
    /**
     * @var string
     * 认证电话
     */
    public $telephone = "";
    /**
     * @var string
     * 身份证类型
     */
    public $idCardType = "";
    /**
     * @var string
     * 身份证
     */
    public $idCode = "";
    /**
     * @var string
     * 身份证正面
     */
    public $front = "";
    /**
     * @var string
     * 身份证反面
     */
    public $back = "";
    /**
     * @var string
     * 手持身份证
     */
    public $hold = "";

    public $verifyType = "";
}