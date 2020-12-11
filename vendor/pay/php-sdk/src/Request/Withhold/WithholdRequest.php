<?php

namespace PayCenter\Request\Withhold;

use PayCenter\Request\Request;
use PayCenter\Request\SetContentTrait;
use PayCenter\Response\WithholdResponse;

abstract class WithholdRequest extends Request
{
    use SetContentTrait;

    //余额支付
    const PAY_METHOD_BALANCE = 1;
    //微信支付
    const PAY_METHOD_WECHAT = 2;
    //货款支付
    const PAY_METHOD_RESIDUE = 8;
    //店铺保证金支付
    const PAY_METHOD_BAIL = 9;

    public function request()
    {
        if (empty($this->payMethod) && empty($this->preferPayMethods)) {
            $this->payMethod = self::PAY_METHOD_BALANCE;
        }
        return new WithholdResponse(parent::request());
    }

    /**
     * @param int $money
     * @return WithholdRequest
     */
    public function setMoney(int $money): WithholdRequest
    {
        $this->money = $money;
        return $this;
    }

    /**
     * @param int $fee
     * @return WithholdRequest
     */
    public function setFee(int $fee): WithholdRequest
    {
        $this->fee = $fee;
        return $this;
    }

    /**
     * @param int $feeType
     * @return WithholdRequest
     */
    public function setFeeType(int $feeType): WithholdRequest
    {
        $this->feeType = $feeType;
        return $this;
    }

    /**
     * @param int $userinfoId
     * @return WithholdRequest
     */
    public function setUserinfoId(int $userinfoId): WithholdRequest
    {
        $this->userinfoId = $userinfoId;
        return $this;
    }

    /**
     * @param string $notifyUrl
     * @return WithholdRequest
     */
    public function setNotifyUrl(string $notifyUrl): WithholdRequest
    {
        $this->notifyUrl = $notifyUrl;
        return $this;
    }

    /**
     * @param string $businessOrderNo
     * @return WithholdRequest
     */
    public function setBusinessOrderNo(string $businessOrderNo): WithholdRequest
    {
        $this->businessOrderNo = $businessOrderNo;
        return $this;
    }

    /**
     * @param int $payMethod
     * @return WithholdRequest
     */
    public function setPayMethod(int $payMethod): WithholdRequest
    {
        $this->payMethod = $payMethod;
        return $this;
    }

    /**
     * @param int ...$payMethod
     * @return WithholdRequest
     */
    public function setPreferPayMethods(...$payMethod): WithholdRequest
    {
        $this->preferPayMethods = implode(',', $payMethod);
        return $this;
    }
}
