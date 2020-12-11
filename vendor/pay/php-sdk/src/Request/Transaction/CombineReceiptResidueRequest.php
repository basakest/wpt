<?php
namespace PayCenter\Request\Transaction;


/**
 * 合并支付确认到货款账户
 * Class CombineReceiptResidueRequest
 * @package PayCenter\Request\Transaction
 */
class CombineReceiptResidueRequest extends ReceiptRequest
{
    const PATH = 'api/v1.0/transaction/combine-receipt-residue';


    /**
     * 到账商户的用户ID
     * @param int $toUserinfoId
     * @return CombineReceiptResidueRequest
     */
    public function setToUserinfoId(int $toUserinfoId): self
    {
        $this->toUserinfoId = $toUserinfoId;
        return $this;
    }


    /**
     * 子订单对应的外部交易订单号
     * @param string $subOutTradeNo
     * @return CombineReceiptResidueRequest
     */
    public function setSubOutTradeNo(string $subOutTradeNo): self
    {
        $this->subOutTradeNo = $subOutTradeNo;
        return $this;
    }

}