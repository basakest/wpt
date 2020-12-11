<?php

namespace PayCenter\Response;

class WithholdResponse extends Response
{
    /**
     * 支付订单号
     * @return string
     */
    public function getOrderNo(): string
    {
        return $this->orderNo;
    }

    /**
     * @return string
     */
    public function getOutTradeNo(): string
    {
        return $this->outTradeNo;
    }
}
