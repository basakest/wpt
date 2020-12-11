<?php

namespace PayCenter\Response\Withdraw;

use PayCenter\Response\Response;

class InfoResponse extends Response
{
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
    public function getAccountType(): int
    {
        return $this->accountType;
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
     * @return int
     */
    public function getPayMethod(): int
    {
        return $this->payMethod;
    }

    /**
     * @return int
     */
    public function getPayAccount(): int
    {
        return $this->payAccount;
    }

    /**
     * @return \stdClass
     */
    public function getBankCard(): \stdClass
    {
        return $this->bankCard;
    }

    /**
     * @return \stdClass
     */
    public function getContent(): \stdClass
    {
        return $this->content;
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
    public function getCreateTime(): int
    {
        return $this->createTime;
    }
}
