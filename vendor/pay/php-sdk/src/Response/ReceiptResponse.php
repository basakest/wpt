<?php

namespace PayCenter\Response;

class ReceiptResponse extends Response
{
    /**
     * 转账订单号
     * @return string
     */
    public function getOrderNo(): string
    {
        return $this->orderNo;
    }

    /**
     * 第三方单号
     * @return string
     */
    public function getOutTradeNo(): string
    {
        return $this->outTradeNo;
    }

    /**
     * 第三方子交易单号
     * @return string
     */
    public function getSubOutTradeNo(): string
    {
        return $this->subOutTradeNo ?? '';
    }
}
