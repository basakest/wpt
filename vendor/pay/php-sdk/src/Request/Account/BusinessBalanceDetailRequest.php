<?php

namespace PayCenter\Request\Account;

class BusinessBalanceDetailRequest extends AccountRequest
{
    const PATH = 'api/v1.0/account/business-balance-detail';

    public function __construct(int $userinfoId, int $id = 0)
    {
        parent::__construct($userinfoId);
        if ($id) {
            $this->id = $id;
        }
    }

    /**
     * @param string $orderNo
     * @return self
     */
    public function setOrderNo(string $orderNo): self
    {
        $this->orderNo = $orderNo;
        return $this;
    }

    /**
     * @param string $outTradeNo
     * @return self
     */
    public function setOutTradeNo(string $outTradeNo): self
    {
        $this->outTradeNo = $outTradeNo;
        return $this;
    }

    /**
     * @param string $balanceType
     * @return self
     */
    public function setBalanceType(string $balanceType): self
    {
        $this->balanceType = $balanceType;
        return $this;
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

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
}
