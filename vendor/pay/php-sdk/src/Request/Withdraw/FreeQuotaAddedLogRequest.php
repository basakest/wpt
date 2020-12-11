<?php

namespace PayCenter\Request\Withdraw;

use PayCenter\Request\ListRequestTrait;
use PayCenter\Request\Request;
use PayCenter\Response\ListResponse;

class FreeQuotaAddedLogRequest extends Request
{
    use ListRequestTrait;

    const PATH = 'api/v1.0/withdraw/free-quota-added-log';

    /**
     * @return ListResponse
     * @throws \PayCenter\Exception\Exception
     */
    public function request(): ListResponse
    {
        return new ListResponse(parent::request());
    }

    /**
     * @param int $userinfoId
     * @return self
     */
    public function setUserinfoId(int $userinfoId): self
    {
        $this->userinfoId = $userinfoId;
        return $this;
    }
}
