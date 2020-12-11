<?php

namespace PayCenter\Request\Withdraw;

use PayCenter\Request\Request;
use PayCenter\Response\Withdraw\ApplyResponse;

class ApplyWithdrawRequest extends Request
{
    const PATH = 'api/v1.0/withdraw/apply-withdraw';

    //允许提现的方式
    const WITHDRAW_METHOD_WECHAT = 2;
    const WITHDRAW_METHOD_BANKCARD = 4;
    const WITHDRAW_METHOD_ALIPAY = 3;

    /**
     * @return ApplyResponse
     * @throws \PayCenter\Exception\Exception
     */
    public function request(): ApplyResponse
    {
        return new ApplyResponse(parent::request());
    }

    /**
     * Set the value of money
     *
     * @param int $money
     * @return  static
     */
    public function setMoney(int $money)
    {
        $this->money = $money;

        return $this;
    }

    /**
     * Set the value of fee
     *
     * @param int $fee
     * @return  static
     */
    public function setFee(int $fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Set the value of userinfoId
     *
     * @param int $userinfoId
     * @return  static
     */
    public function setUserinfoId(int $userinfoId)
    {
        $this->userinfoId = $userinfoId;

        return $this;
    }

    /**
     * Set the value of withdrawMethod
     *
     * @param int $withdrawMethod
     * @return  static
     */
    public function setWithdrawMethod(int $withdrawMethod)
    {
        $this->withdrawMethod = $withdrawMethod;

        return $this;
    }

    /**
     * Set the value of ip
     *
     * @param string $ip
     * @return  static
     */
    public function setIp(string $ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Set the value of bankcardId
     *
     * @param int $bankcardId
     * @return  static
     */
    public function setBankcardId(int $bankcardId)
    {
        $this->bankcardId = $bankcardId;

        return $this;
    }

    /**
     * Set the value of contentJson
     *
     * @param string $contentJson
     * @return  static
     */
    public function setContentJson(string $contentJson)
    {
        $this->contentJson = $contentJson;

        return $this;
    }

    /**
     * Set the value of content
     *
     * @param mixed $content
     * @return  static
     */
    public function setContent($content)
    {
        $this->contentJson = is_string($content) ? $content : json_encode($content, JSON_UNESCAPED_UNICODE);
        return $this;
    }

    /**
     * Set the value of notifyUrl
     *
     * @param string $notifyUrl
     * @return  static
     */
    public function setNotifyUrl(string $notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;

        return $this;
    }

    /**
     * Set the value of businessOrderNo
     *
     * @param string $businessOrderNo
     * @return  static
     */
    public function setBusinessOrderNo(string $businessOrderNo)
    {
        $this->businessOrderNo = $businessOrderNo;

        return $this;
    }

    /**
     * Set the value of openid
     *
     * @param string $openid
     * @return  static
     */
    public function setOpenid(string $openid)
    {
        $this->openid = $openid;

        return $this;
    }

}
