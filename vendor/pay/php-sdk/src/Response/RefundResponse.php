<?php

namespace PayCenter\Response;

class RefundResponse extends Response
{
    /**
     * 退款订单号
     * @return string
     */
    public function getRefundOutTradeNo(): string
    {
        return $this->refundOutTradeNo;
    }
}
