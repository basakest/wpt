<?php

namespace PayCenter\Request\Withdraw;

class ApplyPmcResidueWithdrawRequest extends ApplyWithdrawRequest
{
    const PATH = 'api/v1.0/withdraw/apply-pmc-residue-withdraw';

    const WITHDRAW_METHOD_WECHAT_PMC = 14;

    public function setWithdrawSegments(array $withdrawSegments)
    {
        $this->withdrawSegments = $withdrawSegments;
        return $this;
    }
}
