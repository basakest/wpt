<?php

namespace PayCenter\Response\Withdraw;

use PayCenter\Response\Response;

class ApplyResponse extends Response
{
    /**
     * 提现单号
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
