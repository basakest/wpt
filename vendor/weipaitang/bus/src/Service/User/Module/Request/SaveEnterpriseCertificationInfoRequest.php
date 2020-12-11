<?php


namespace WptBus\Service\User\Module\Request;


class SaveEnterpriseCertificationInfoRequest extends BaseRequest
{
    /**
     * @var int
     */
    public $uid = 0;

    public $companyName = "";

    public $name = "";

    public $food = "";

    public $idCardType = "";

    public $idCode = "";

    public $front = "";

    public $back = "";

    public $businessLicense = "";

    public $auction = "";

    public $enterpriseType = "";

    public $bizId = 0;
}