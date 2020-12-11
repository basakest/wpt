<?php

namespace PayCenter\Response;

class TransferResponse extends Response
{
    /**
     * 支付系统订单号
     * @return string
     */
    public function getOrderNo(): string
    {
        return $this->orderNo ?? $this->outTradeNo;
    }

    /**
     * 第三方单号
     * @return string
     */
    public function getOutTradeNo(): string
    {
        return $this->outTradeNo;
    }
}
