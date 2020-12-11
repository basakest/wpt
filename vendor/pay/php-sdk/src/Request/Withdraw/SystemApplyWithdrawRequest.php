<?php

namespace PayCenter\Request\Withdraw;

class SystemApplyWithdrawRequest extends ApplyWithdrawRequest
{
    const PATH = 'api/v1.0/withdraw/system-apply-withdraw';

    public function __construct()
    {
        parent::__construct();
        $this->withdrawMethod = self::WITHDRAW_METHOD_BANKCARD;
    }

    /**
     * Set the value of bankCardJson
     *
     * @param string $bankCardJson
     * @return static
     */
    public function setBankCardJson(string $bankCardJson)
    {
        $this->bankCardJson = $bankCardJson;

        return $this;
    }

    /**
     * Set the value of bankCard
     *
     * @param $bankCard
     * @return static
     */
    public function setBankCard($bankCard)
    {
        $this->bankCardJson = json_encode($bankCard, JSON_UNESCAPED_UNICODE);

        return $this;
    }

    /**
     * @param string $userIdentifier
     * @return static
     */
    public function setUserIdentifier(string $userIdentifier)
    {
        $this->userIdentifier = $userIdentifier;

        return $this;
    }
}
