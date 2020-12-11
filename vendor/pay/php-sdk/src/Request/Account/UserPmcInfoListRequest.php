<?php

namespace PayCenter\Request\Account;

use PayCenter\Request\Request;

class UserPmcInfoListRequest extends Request
{
    const PATH = 'api/v1.0/account/user-pmc-info-list';

    public function __construct(int $userinfoId)
    {
        parent::__construct();
        $this->userinfoId = $userinfoId;
    }

    /**
     * @return int
     * @throws \PayCenter\Exception\Exception
     */
    public function request()
    {
        return parent::request()->data;
    }
}
