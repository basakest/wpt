<?php

namespace PayCenter\Request\Refund;

use PayCenter\Request\Request;
use PayCenter\Response\RefundResponse;

/**
 * 合并支付退款
 * Class CombineRefundRequest
 * @package PayCenter\Request\Refund
 */
class CombineRefundRequest extends RefundRequest
{
    const PATH = 'api/v1.0/refund/combine-refund';

    /**
     * 合并支付子订单的外部支付交易号
     * @param string $subOutTradeNo
     * @return RefundRequest
     */
    public function setSubOutTradeNo(string $subOutTradeNo): self
    {
        $this->subOutTradeNo = $subOutTradeNo;
        return $this;
    }
}
