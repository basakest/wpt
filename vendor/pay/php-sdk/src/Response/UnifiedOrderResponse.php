<?php

namespace PayCenter\Response;

class UnifiedOrderResponse extends Response
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
     * 支付密码随机串
     * @return string
     */
    public function getNonceStr(): string
    {
        return $this->nonceStr;
    }
}
