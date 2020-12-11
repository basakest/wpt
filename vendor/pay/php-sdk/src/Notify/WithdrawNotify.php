<?php

namespace PayCenter\Notify;

class WithdrawNotify extends Notify
{
    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getProduct(): int
    {
        return $this->product;
    }

    /**
     * @return int
     */
    public function getWithdrawMethod(): int
    {
        return $this->withdrawMethod;
    }

    /**
     * @return string
     */
    public function getOutTradeNo(): string
    {
        return $this->outTradeNo;
    }

    /**
     * @return string
     */
    public function getOrderNo(): string
    {
        return $this->orderNo;
    }

    /**
     * @return string
     */
    public function getBusinessOrderNo(): string
    {
        return $this->businessOrderNo;
    }

    /**
     * @return int
     */
    public function getActualMoney(): int
    {
        return $this->actualMoney;
    }

    /**
     * @return int
     */
    public function getMoney(): int
    {
        return $this->money;
    }

    /**
     * @return int
     */
    public function getFee(): int
    {
        return $this->fee;
    }

    /**
     * @return string
     */
    public function getRemarks(): string
    {
        return $this->remarks;
    }

    /**
     * @return int
     */
    public function getAccountType(): int
    {
        return $this->accountType;
    }

    /**
     * @return int
     */
    public function getPayMethod(): int
    {
        return $this->payMethod;
    }

    /**
     * @return mixed
     */
    public function getBankCardJson()
    {
        return $this->bankCardJson;
    }

    /**
     * @return int
     */
    public function getUserinfoId(): int
    {
        return $this->userinfoId;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    /**
     * @return int
     */
    public function getCreateTime(): int
    {
        return $this->createTime;
    }
}
