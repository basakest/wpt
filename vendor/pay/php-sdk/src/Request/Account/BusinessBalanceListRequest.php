<?php

namespace PayCenter\Request\Account;

use PayCenter\Request\ListRequestTrait;

class BusinessBalanceListRequest extends AccountRequest
{
    use ListRequestTrait;

    const PATH = 'api/v1.0/account/business-balance-list';

    /**
     * @param mixed $accountType
     * @return BusinessBalanceListRequest
     */
    public function setAccountType(...$accountType): BusinessBalanceListRequest
    {
        $this->accountType = implode(',', $accountType);
        return $this;
    }

    /**
     * @param int $lastId
     * @return BusinessBalanceListRequest
     */
    public function setLastId(int $lastId): BusinessBalanceListRequest
    {
        $this->lastId = $lastId;
        return $this;
    }

    /**
     * @param mixed $balanceType
     * @return BusinessBalanceListRequest
     */
    public function setBalanceType(...$balanceType): BusinessBalanceListRequest
    {
        $this->balanceType = implode(',', $balanceType);
        return $this;
    }

    /**
     * @param mixed $businessType
     * @return BusinessBalanceListRequest
     */
    public function setBusinessType(...$businessType): BusinessBalanceListRequest
    {
        $this->businessType = implode(',', $businessType);
        return $this;
    }

    /**
     * @param mixed $balanceStatus
     * @return BusinessBalanceListRequest
     */
    public function setBalanceStatus(...$balanceStatus): BusinessBalanceListRequest
    {
        $this->balanceStatus = implode(',', $balanceStatus);
        return $this;
    }

    /**
     * @param mixed $payMethod
     * @return BusinessBalanceListRequest
     */
    public function setPayMethod(...$payMethod): BusinessBalanceListRequest
    {
        $this->payMethod = implode(',', $payMethod);
        return $this;
    }

    /**
     * @param bool $needAmount
     * @return BusinessBalanceListRequest
     */
    public function setNeedAmount(bool $needAmount = true): BusinessBalanceListRequest
    {
        $this->needAmount = $needAmount;
        return $this;
    }

    /**
     * @param string $target
     * @return BusinessBalanceListRequest
     */
    public function setTarget(string $target): BusinessBalanceListRequest
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @param string ...$targetUri
     * @return BusinessBalanceListRequest
     */
    public function setTargetUri(string ...$targetUri): BusinessBalanceListRequest
    {
        $this->targetUri = implode(',', $targetUri);
        return $this;
    }

    /**
     * @param int $startTime
     * @return BusinessBalanceListRequest
     */
    public function setStartTime(int $startTime): BusinessBalanceListRequest
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * @param int $endTime
     * @return BusinessBalanceListRequest
     */
    public function setEndTime(int $endTime): BusinessBalanceListRequest
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @param bool $isThawed
     * @return BusinessBalanceListRequest
     */
    public function setIsThawed(bool $isThawed = true): BusinessBalanceListRequest
    {
        $this->isThawed = $isThawed;
        return $this;
    }

    /**
     * @param string $timeFilter
     * @return BusinessBalanceListRequest
     */
    public function setTimeFilter(string $timeFilter): BusinessBalanceListRequest
    {
        $this->timeFilter = $timeFilter;
        return $this;
    }
}
