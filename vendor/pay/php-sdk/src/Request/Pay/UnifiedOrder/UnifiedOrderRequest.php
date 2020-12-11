<?php

namespace PayCenter\Request\Pay\UnifiedOrder;

use PayCenter\Request\Request;
use PayCenter\Request\SetContentTrait;
use PayCenter\Response\UnifiedOrderResponse;

abstract class UnifiedOrderRequest extends Request
{
    use SetContentTrait;

    public function request()
    {
        return new UnifiedOrderResponse(parent::request());
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
     * @param int $fee
     * @return static
     */
    public function setFee(int $fee): self
    {
        $this->fee = $fee;
        return $this;
    }

    /**
     * @param int $feeType
     * @return static
     */
    public function setFeeType(int $feeType): self
    {
        $this->feeType = $feeType;
        return $this;
    }

    /**
     * @param int $userinfoId
     * @return static
     */
    public function setUserinfoId(int $userinfoId): self
    {
        $this->userinfoId = $userinfoId;
        return $this;
    }

    /**
     * @param array $appointPayMethodList
     * @return static
     */
    public function setAppointPayMethodList(array $appointPayMethodList): self
    {
        $this->appointPayMethodList = $appointPayMethodList;
        return $this;
    }

    /**
     * @param array $ignorePayMethodList
     * @return static
     */
    public function setIgnorePayMethodList(array $ignorePayMethodList): self
    {
        $this->ignorePayMethodList = $ignorePayMethodList;
        return $this;
    }

    /**
     * @param int $expiredTime
     * @return static
     */
    public function setExpiredTime(int $expiredTime): self
    {
        $this->expiredTime = $expiredTime;
        return $this;
    }

    /**
     * @param string $notifyUrl
     * @return static
     */
    public function setNotifyUrl(string $notifyUrl): self
    {
        $this->notifyUrl = $notifyUrl;
        return $this;
    }

    /**
     * @param string $returnUrl
     * @return static
     */
    public function setReturnUrl(string $returnUrl): self
    {
        $this->returnUrl = $returnUrl;
        return $this;
    }

    /**
     * @param string $attach
     * @return static
     */
    public function setAttach(string $attach): self
    {
        $this->attach = $attach;
        return $this;
    }

    /**
     * @param int $limitCC
     * @return static
     */
    public function setLimitCC(int $limitCC): self
    {
        $this->limitCC = $limitCC;
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
     * @param int $subsidy
     * @return static
     */
    public function setSubsidy(int $subsidy): self
    {
        $this->subsidy = $subsidy;
        return $this;
    }

    /**
     * 是否允许通过合规支付
     * @param bool $allowPMC
     * @return static
     */
    public function setAllowPMC($allowPMC = true): self
    {
        $this->allowPMC = $allowPMC;
        return $this;
    }

    /**
     * 添加收款/分账方用户ID（判断用户是否合规进件）
     * @param int ...$userinfoIds
     * @return static
     */
    public function addReceiverUserinfoId(int ...$userinfoIds): self
    {
        $this->receiverUserinfoIds = array_merge($this->receiverUserinfoIds ?? [], $userinfoIds);
        return $this;
    }
}
