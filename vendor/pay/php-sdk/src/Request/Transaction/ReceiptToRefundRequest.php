<?php
namespace PayCenter\Request\Transaction;

use PayCenter\Request\Request;
use PayCenter\Response\ReceiptToRefundResponse;

class ReceiptToRefundRequest extends Request
{

    const PATH = 'api/v1.0/transaction/receipted-to-refund';
    public function request()
    {
        return new ReceiptToRefundResponse(parent::request());
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
     * @param string $refundBusinessOrderNo
     * @return ReceiptToRefundRequest
     */
    public function setRefundBusinessOrderNo(string $refundBusinessOrderNo): self
    {
        $this->refundBusinessOrderNo = $refundBusinessOrderNo;
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
     * @param string $refundNo
     * @return ReceiptToRefundRequest
     */
    public function setRefundNo(string $refundNo): self
    {
        $this->refundNo = $refundNo;
        return $this;
    }

    /**
     * @param string $notifyUrl
     * @return ReceiptToRefundRequest
     */
    public function setNotifyUrl(string $notifyUrl): self
    {
        $this->notifyUrl = $notifyUrl;
        return $this;
    }
}