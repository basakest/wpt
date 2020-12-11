<?php

namespace PayCenter\Request\Transaction;

/**
 * by-fangcg部分确认收货SDK
 * Class PartReceiptResidueRequest
 * @package PayCenter\Request\Transaction
 */
class PartReceiptResidueRequest extends ReceiptRequest
{
    const PATH = 'api/v1.0/transaction/part-receipt-residue';

    public function setMultiOutTradeNo(array $multiOutTradeNo): ReceiptRequest
    {
        throw new \Exception('不支持该参数');
    }
}
