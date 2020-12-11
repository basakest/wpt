<?php

namespace PayCenter\Request\Refund;

use PayCenter\Request\Request;
use PayCenter\Response\RefundResponse;

class RefundRequest extends Request
{
    const PATH = 'api/v1.0/refund/refund';

    public function request(): RefundResponse
    {
        return new RefundResponse(parent::request());
    }

    /**
     * @param string $orderNo
     * @return $this
     */
    public function setOrderNo(string $orderNo): self
    {
        $this->orderNo = $orderNo;
        return $this;
    }

    /**
     * @param string $outTradeNo
     * @return $this
     */
    public function setOutTradeNo(string $outTradeNo): self
    {
        $this->outTradeNo = $outTradeNo;
        return $this;
    }

    /**
     * @param int $money
     * @return $this
     */
    public function setMoney(int $money): self
    {
        $this->money = $money;
        return $this;
    }

    /**
     * @param int $fee
     * @return $this
     */
    public function setFee(int $fee): self
    {
        $this->fee = $fee;
        return $this;
    }

    /**
     * @param int $subsidy
     * @return $this
     */
    public function setSubsidy(int $subsidy): self
    {
        $this->subsidy = $subsidy;
        return $this;
    }

    /**
     * @param string $notifyUrl
     * @return $this
     */
    public function setNotifyUrl(string $notifyUrl): self
    {
        $this->notifyUrl = $notifyUrl;
        return $this;
    }

    /**
     * @param string $refundBusinessOrderNo
     * @return $this
     */
    public function setRefundBusinessOrderNo(string $refundBusinessOrderNo): self
    {
        $this->refundBusinessOrderNo = $refundBusinessOrderNo;
        return $this;
    }

    public function setRefundJson($refundJson): self
    {
        $this->refundJson = is_string($refundJson) ? $refundJson : json_encode($refundJson, JSON_UNESCAPED_UNICODE);
        return $this;
    }
}
