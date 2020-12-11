<?php

namespace PayCenter\Response;

class ReceiptToRefundResponse extends Response
{
    /**退款订单号转账订单号
     * @return string
     */
    public function getRefundOutTradeNo(): string
    {
        return $this->refundOutTradeNo;
    }

}
