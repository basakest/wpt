<?php

namespace PayCenter\Notify;

class PayNotify extends Notify
{
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
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getOutTradeNo(): string
    {
        return $this->outTradeNo;
    }

    /**
     * @return int
     */
    public function getTotalFee(): int
    {
        return $this->totalFee;
    }

    /**
     * @return string
     */
    public function getBusinessPayMethod(): string
    {
        return $this->businessPayMethod;
    }

    /**
     * @return int
     */
    public function getPayMethod(): int
    {
        return $this->payMethod;
    }

    /**
     * @return string
     */
    public function getCardType(): string
    {
        return $this->cardType;
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
    public function getOpenid(): string
    {
        return $this->openid;
    }

    /**
     * @return string
     */
    public function getBusinessOrderNo(): string
    {
        return $this->businessOrderNo;
    }

    /**
     * @return string
     */
    public function getAttach(): string
    {
        return $this->attach;
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
    public function getBankCardJson(): string
    {
        return $this->bankCardJson;
    }

    /**
     * @return mixed
     */
    public function getResourceData()
    {
        return $this->resourceData;
    }

    /**
     * @return string
     */
    public function getSignature(): string
    {
        return $this->signature;
    }

    /**
     * @return int
     */
    public function getPaidTime(): int
    {
        return $this->paidTime;
    }

    /**
     * @return int
     */
    public function getUserinfoId(): int
    {
        return $this->userinfoId;
    }

    /**
     * @return int
     */
    public function getPayUserinfoId(): int
    {
        return $this->payUserinfoId;
    }

    /**
     * @return int
     */
    public function getPlatform(): int
    {
        return $this->platform;
    }

    public function getSubOrders(): array
    {
        return $this->subOrders ?? [];
    }
}
