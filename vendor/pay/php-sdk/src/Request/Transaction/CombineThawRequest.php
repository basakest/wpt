<?php
namespace PayCenter\Request\Transaction;


/**
 * 合并支付解冻到货款账户
 * Class CombineThawRequest
 * @package PayCenter\Request\Transaction
 */
class CombineThawRequest extends OrderThawRequest
{
    const PATH = 'api/v1.0/transaction/combine-thaw';

    /**
     * 子订单对应的外部交易订单号
     * @param string $subOutTradeNo
     * @return CombineThawRequest
     */
    public function setSubOutTradeNo(string $subOutTradeNo): self
    {
        $this->subOutTradeNo = $subOutTradeNo;
        return $this;
    }
}