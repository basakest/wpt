<?php
namespace PayCenter\Request\Transaction;

use PayCenter\Request\Request;
use PayCenter\Response\ReceiptResponse;

/**
 * auth by-fangcg
 * 订单解冻
 * Class OrderThawRequest
 * @package PayCenter\Request\Transaction
 */
class OrderThawRequest extends Request
{
    const PATH = 'api/v1.0/transaction/order-thaw';

    public function request()
    {
        return new ReceiptResponse(parent::request());
    }

    /**
     * @param int $money
     * @return static
     */
    public function setMoney(int $money): self
    {
        $this->money = $money;
        return $this;
    }

    /**
     * @param string $contentJson
     * @return static
     */
    public function setContentJson(string $contentJson): self
    {
        $this->contentJson = $contentJson;
        return $this;
    }

    /**
     * @param string $outTradeNo
     * @return static
     */
    public function setOutTradeNo(string $outTradeNo): self
    {
        $this->outTradeNo = $outTradeNo;
        return $this;
    }

    /**
     * @param string $businessOrderNo
     * @return static
     */
    public function setBusinessOrderNo(string $businessOrderNo): self
    {
        $this->businessOrderNo = $businessOrderNo;
        return $this;
    }
}
