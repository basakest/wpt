<?php

namespace WptBus\Service\User\Module\Request\Account;


use WptBus\Service\User\Module\Request\BaseRequest;

class GetLogoffListRequest extends BaseRequest
{
    public $startTime = 0;
    public $endTime = 0;
    public $reasons = []; // int数组
    public $states = []; // int数组
    public $telephone = "";
    public $userinfoId = 0;
    public $limit = 20;
    public $offset = 0;
    public $sortField = "";
    public $sortStyle = "";
}