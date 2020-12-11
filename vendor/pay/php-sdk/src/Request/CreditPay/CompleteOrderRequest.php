<?php

namespace PayCenter\Request\CreditPay;

use PayCenter\Request\Request;
use PayCenter\Response\CreditPay\CompleteOrderResponse;

class CompleteOrderRequest extends Request
{
    const PATH = 'api/v1.0/creditpay/complete';

    /**
     * @param string $outTradeNo
     * @return CompleteOrderRequest
     */
    public function setOutTradeNo(string $outTradeNo): CompleteOrderRequest
    {
        $this->outTradeNo = $outTradeNo;
        return $this;
    }

    /**
     * @param int $money
     * @return CompleteOrderRequest
     */
    public function setMoney(int $money): CompleteOrderRequest
    {
        $this->money = $money;
        return $this;
    }

    public function request()
    {
        return new CompleteOrderResponse(parent::request());
    }
}
