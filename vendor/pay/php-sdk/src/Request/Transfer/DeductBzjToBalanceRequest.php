<?php

namespace PayCenter\Request\Transfer;

class DeductBzjToBalanceRequest extends TransferRequest
{
    const PATH = 'api/v1.0/transfer/deduct-bzj-to-balance';

    /**
     * 设置支付订单号
     * @return  self
     */
    public function setOrderNo($orderNo)
    {
        $this->orderNo = $orderNo;
        return $this;
    }
}
