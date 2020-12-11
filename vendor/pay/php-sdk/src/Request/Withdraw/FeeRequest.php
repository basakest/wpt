<?php

namespace PayCenter\Request\Withdraw;

use PayCenter\Request\Account\AccountRequest;
use PayCenter\Request\Request;
use PayCenter\Response\Withdraw\FeeResponse;

class FeeRequest extends Request
{
    const PATH = 'api/v1.0/withdraw/fee';

    public function __construct(int $userinfoId, int $money = 0, int $accountType = AccountRequest::BALANCE_ACCOUNT)
    {
        parent::__construct();
        $this->setUserinfoId($userinfoId)->setMoney($money)->setAccountType($accountType);
    }

    /**
     * @return FeeResponse
     * @throws \PayCenter\Exception\Exception
     */
    public function request(): FeeResponse
    {
        return new FeeResponse(parent::request());
    }

    /**
     * @param int $money
     * @return FeeRequest
     */
    public function setMoney(int $money): FeeRequest
    {
        $this->money = $money;
        return $this;
    }

    /**
     * @param int $accountType
     * @return FeeRequest
     */
    public function setAccountType(int $accountType): FeeRequest
    {
        $this->accountType = $accountType;
        return $this;
    }

    /**
     * @param int $userinfoId
     * @return FeeRequest
     */
    public function setUserinfoId(int $userinfoId): FeeRequest
    {
        $this->userinfoId = $userinfoId;
        return $this;
    }
}
