<?php

namespace PayCenter\Response\Withdraw;

use PayCenter\Response\Response;

class FeeResponse extends Response
{
    /**
     * 剩余免费提现额度
     * @return int
     */
    public function getSurplusFreeQuota(): int
    {
        return $this->surplusFreeQuota;
    }

    /**
     * 提现手续费率
     * @return int
     */
    public function getRate(): int
    {
        return $this->rate;
    }

    /**
     * 手续费
     * @return int
     */
    public function getFee(): int
    {
        return $this->fee;
    }
}
