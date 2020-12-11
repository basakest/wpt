<?php
namespace PayCenter\Request\Transaction;

use PayCenter\Request\Request;
use PayCenter\Response\ReceiptResponse;

class ReceiptRequest extends Request
{

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

    /**
     * @param int $fee
     * @return static
     */
    public function setFee(int $fee): self
    {
        $this->fee = $fee;
        return $this;
    }

    /**
     * @param int $multiOutTradeNo
     * @return static
     */
    public function setMultiOutTradeNo(array $multiOutTradeNo): self
    {
        $this->multiOutTradeNo = $multiOutTradeNo;
        return $this;
    }
}