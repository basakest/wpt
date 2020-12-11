<?php

namespace PayCenter\Request\Account;

class BalanceRequest extends AccountRequest
{
    const PATH = 'api/v1.0/account/balance';

    /**
     * @return int
     * @throws \PayCenter\Exception\Exception
     */
    public function request()
    {
        return parent::request()->money ?? 0;
    }
}
