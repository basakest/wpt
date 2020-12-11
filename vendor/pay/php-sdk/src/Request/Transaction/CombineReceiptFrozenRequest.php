<?php
namespace PayCenter\Request\Transaction;


/**
 * 合并支付确认到不可用账户
 * Class CombineReceiptFrozenRequest
 * @package PayCenter\Request\Transaction
 */
class CombineReceiptFrozenRequest extends CombineReceiptResidueRequest
{
    const PATH = 'api/v1.0/transaction/combine-receipt-frozen';
}