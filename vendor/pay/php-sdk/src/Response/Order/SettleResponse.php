<?php

namespace PayCenter\Response\Order;

use PayCenter\Response\Response;

class SettleResponse extends Response
{
    /**
     * 结算订单号
     * @return string
     */
    public function getOrderNo(): string
    {
        return $this->orderNo;
    }
}
