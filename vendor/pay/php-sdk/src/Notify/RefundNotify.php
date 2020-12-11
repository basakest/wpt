<?php

namespace PayCenter\Notify;

class RefundNotify extends Notify
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
    public function getRefundBusinessOrderNo(): string
    {
        return $this->refundBusinessOrderNo;
    }

    /**
     * @return string
     */
    public function getOutRefundNo(): string
    {
        return $this->outRefundNo;
    }

    /**
     * @return mixed
     */
    public function getResourceData()
    {
        return $this->resourceData;
    }

    /**
     * @return int
     */
    public function getRefundMoney(): int
    {
        return $this->refundMoney;
    }

    /**
     * @return int
     */
    public function getPayMoney(): int
    {
        return $this->payMoney;
    }

    /**
     * @return int
     */
    public function getSubsidy(): int
    {
        return $this->subsidy;
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
}
