<?php

namespace PayCenter\Request\Account;

class MultiListRequest extends AccountRequest
{
    const PATH = 'api/v1.0/account/multi-list';

    public function __construct(array $userinfoIds, array $accountTypes = [])
    {
        parent::__construct(0);

        $this->userinfoIds = $userinfoIds;
        $this->accountTypes = $accountTypes;
    }

    /**
     * @return array
     * @throws \PayCenter\Exception\Exception
     */
    public function request()
    {
        return (array)parent::request()->items;
    }
}
