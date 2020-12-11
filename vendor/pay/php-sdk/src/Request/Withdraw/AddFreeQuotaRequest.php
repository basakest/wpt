<?php

namespace PayCenter\Request\Withdraw;

use PayCenter\Request\Account\AccountRequest;
use PayCenter\Request\Request;

class AddFreeQuotaRequest extends Request
{
    const PATH = 'api/v1.0/withdraw/add-free-quota';

    public function __construct(int $userinfoId, int $quota = 0, int $accountType = AccountRequest::BALANCE_ACCOUNT)
    {
        parent::__construct();
        $this->setUserinfoId($userinfoId)->setQuota($quota)->setAccountType($accountType);
    }

    /**
     * @param int $quota
     * @return AddFreeQuotaRequest
     */
    public function setQuota(int $quota): AddFreeQuotaRequest
    {
        $this->quota = $quota;
        return $this;
    }

    /**
     * @param int $accountType
     * @return AddFreeQuotaRequest
     */
    public function setAccountType(int $accountType): AddFreeQuotaRequest
    {
        $this->accountType = $accountType;
        return $this;
    }

    /**
     * @param int $userinfoId
     * @return AddFreeQuotaRequest
     */
    public function setUserinfoId(int $userinfoId): AddFreeQuotaRequest
    {
        $this->userinfoId = $userinfoId;
        return $this;
    }

    /**
     * @param string $remarks
     * @return AddFreeQuotaRequest
     */
    public function setRemarks(string $remarks): AddFreeQuotaRequest
    {
        $this->remarks = $remarks;
        return $this;
    }
}
