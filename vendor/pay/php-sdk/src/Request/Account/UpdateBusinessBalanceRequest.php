<?php

namespace PayCenter\Request\Account;

use PayCenter\Request\SetContentTrait;

class UpdateBusinessBalanceRequest extends BusinessBalanceDetailRequest
{
    use SetContentTrait;

    const PATH = 'api/v1.0/account/update-business-balance';

    /**
     * @param string $target
     * @return UpdateBusinessBalanceRequest
     */
    public function setTarget(string $target): UpdateBusinessBalanceRequest
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @param int $targetId
     * @return UpdateBusinessBalanceRequest
     */
    public function setTargetId(int $targetId): UpdateBusinessBalanceRequest
    {
        $this->targetId = $targetId;
        return $this;
    }

    /**
     * @param string $targetUri
     * @return UpdateBusinessBalanceRequest
     */
    public function setTargetUri(string $targetUri): UpdateBusinessBalanceRequest
    {
        $this->targetUri = $targetUri;
        return $this;
    }

    /**
     * @param string $balanceStatus
     * @return UpdateBusinessBalanceRequest
     */
    public function setBalanceStatus(string $balanceStatus): UpdateBusinessBalanceRequest
    {
        $this->balanceStatus = $balanceStatus;
        return $this;
    }

    /**
     * @param string $businessType
     * @return UpdateBusinessBalanceRequest
     */
    public function setBusinessType(string $businessType): UpdateBusinessBalanceRequest
    {
        $this->businessType = $businessType;
        return $this;
    }
}
