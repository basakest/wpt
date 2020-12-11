<?php


namespace WptBus\Service\User\Module\Request;


class SaveShopInfoRequest extends BaseRequest
{
    /**
     * @var int
     */
    public $shopID=0;
    /**
     * @var string
     */
    public $introduce = "";
    /**
     * @var string
     */
    public $logo = "";
}