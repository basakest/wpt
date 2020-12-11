<?php

namespace PayCenter\Request\Transfer;

use PayCenter\Request\Request;
use PayCenter\Response\TransferResponse;
use PayCenter\Request\SetContentTrait;

abstract class TransferRequest extends Request
{
    use SetContentTrait;

    /**
     * @return TransferResponse
     * @throws \PayCenter\Exception\Exception
     */
    public function request(): TransferResponse
    {
        return new TransferResponse(parent::request());
    }

    /**
     * 设置转出用户ID
     * @param int $userinfoId
     * @return static
     */
    public function setUserinfoId(int $userinfoId): self
    {
        $this->userinfoId = $userinfoId;
        return $this;
    }

    /**
     * 设置转入用户ID
     * @param int $toUserinfoId
     * @return static
     */
    public function setToUserinfoId(int $toUserinfoId): self
    {
        $this->toUserinfoId = $toUserinfoId;
        return $this;
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
     * 设置手续费
     * @return  self
     */
    public function setFee($fee)
    {
        $this->fee = $fee;
        return $this;
    }

    /**
     * 设置原支付订单号
     * @return  self
     */
    public function setOrderNo($orderNo)
    {
        $this->orderNo = $orderNo;
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
