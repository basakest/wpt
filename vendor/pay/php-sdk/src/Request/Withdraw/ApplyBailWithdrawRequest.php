<?php

namespace PayCenter\Request\Withdraw;

class ApplyBailWithdrawRequest extends ApplyWithdrawRequest
{
    const PATH = 'api/v1.0/withdraw/apply-bail-withdraw';

    const WITHDRAW_METHOD_BALANCE = 1;

    public function __construct()
    {
        parent::__construct();
        $this->withdrawMethod = self::WITHDRAW_METHOD_BALANCE;
    }
}
