<?php
namespace PayCenter\Request\Transaction;


use PayCenter\Exception\Exception;

/**
 * by-fangcg红包领取确认
 * Class OnwayToBalanceRequest
 * @package PayCenter\Request\Transaction
 */
class OnwayToBalanceRequest extends ReceiptRequest
{

    const PATH = 'api/v1.0/transaction/onway-to-balance';

    public function setToUserinfoId(int $toUserinfoId)
    {
        $this->toUserinfoId = $toUserinfoId;
        return $this;
    }

    public function setMultiOutTradeNo(array $multiOutTradeNo): ReceiptRequest
    {
        throw new \Exception('不支持该参数');
    }
}