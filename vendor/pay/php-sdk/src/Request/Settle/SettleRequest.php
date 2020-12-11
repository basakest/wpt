<?php

namespace PayCenter\Request\Settle;

use PayCenter\Request\Request;
use PayCenter\Request\SetContentTrait;
use PayCenter\Response\Order\SettleResponse;

abstract class SettleRequest extends Request
{
    use SetContentTrait;

    public function request(): SettleResponse
    {
        return new SettleResponse(parent::request());
    }

    /**
     * 结算总金额（包含手续费和分账金额）
     * @param int $money
     * @return static
     */
    public function setMoney(int $money): self
    {
        $this->money = $money;
        return $this;
    }

    /**
     * 设置支付订单号
     * @return static
     */
    public function setOrderNo($orderNo)
    {
        $this->orderNo = $orderNo;
        return $this;
    }

    /**
     * 支付商户单号
     * @param string $outTradeNo
     * @return static
     */
    public function setOutTradeNo(string $outTradeNo): self
    {
        $this->outTradeNo = $outTradeNo;
        return $this;
    }

    /**
     * 业务单号，使用该单号保证接口幂等
     * @param string $businessOrderNo
     * @return static
     */
    public function setBusinessOrderNo(string $businessOrderNo): self
    {
        $this->businessOrderNo = $businessOrderNo;
        return $this;
    }

    /**
     * 设置平台手续费
     * @param int $fee
     * @return static
     */
    public function setFee(int $fee): self
    {
        $this->fee = $fee;
        return $this;
    }

    /**
     * 设置多笔支付单号
     * @param array $multiOutTradeNo
     * @return static
     */
    public function setMultiOutTradeNo(array $multiOutTradeNo): self
    {
        $this->multiOutTradeNo = $multiOutTradeNo;
        return $this;
    }

    /**
     * 合并支付子订单对应的外部交易订单号
     * @param string $subOutTradeNo
     * @return static
     */
    public function setSubOutTradeNo(string $subOutTradeNo): self
    {
        $this->subOutTradeNo = $subOutTradeNo;
        return $this;
    }

    /**
     * 结算至该用户ID
     * @param int $toUserinfoId
     * @return static
     */
    public function setToUserinfoId(int $toUserinfoId): self
    {
        $this->toUserinfoId = $toUserinfoId;
        return $this;
    }

    /**
     * 增加平台分账信息
     * @param int $product
     * @param int $money
     * @return static
     */
    public function addProductProfitSharing(int $product, int $money): self
    {
        if (empty($product)) {
            $product = $this->product;
        }
        $this->profitSharing = array_merge($this->profitSharing ?? [], [compact('product', 'money')]);
        return $this;
    }

    /**
     * 增加用户分账信息
     * @param int $userinfoId
     * @param int $money
     * @return static
     */
    public function addUserProfitSharing(int $userinfoId, int $money): self
    {
        $this->profitSharing = array_merge($this->profitSharing ?? [], [compact('userinfoId', 'money')]);
        return $this;
    }
}
