<?php

namespace PayCenter\Request\Transfer;

use PayCenter\Request\Request;
use PayCenter\Response\TransferResponse;
use PayCenter\Request\SetContentTrait;

class ThawRequest extends Request
{
    use SetContentTrait;

    const PATH = 'api/v1.0/transfer/thaw';

    /**
     * @return TransferResponse
     * @throws \PayCenter\Exception\Exception
     */
    public function request(): TransferResponse
    {
        return new TransferResponse(parent::request());
    }

    /**
     * 设置交易金额
     * @param int $money
     * @return static
     */
    public function setMoney(int $money): self
    {
        $this->money = $money;
        return $this;
    }

    /**
     * 设置外部支付订单号
     * @param string $outTradeNo
     * @return self
     */
    public function setOutTradeNo(string $outTradeNo)
    {
        $this->outTradeNo = $outTradeNo;
        return $this;
    }

    /**
     * 设置业务订单号
     * @param string $businessOrderNo
     * @return static
     */
    public function setBusinessOrderNo(string $businessOrderNo): self
    {
        $this->businessOrderNo = $businessOrderNo;
        return $this;
    }
}
