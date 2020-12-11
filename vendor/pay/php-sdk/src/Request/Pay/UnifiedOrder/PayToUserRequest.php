<?php

namespace PayCenter\Request\Pay\UnifiedOrder;

class PayToUserRequest extends UnifiedOrderRequest
{
    const PATH = 'api/v1.0/pay/pay-to-user';

    /**
     * @param int $toUserinfoId
     * @return static
     */
    public function setToUserinfoId(int $toUserinfoId): self
    {
        $this->toUserinfoId = $toUserinfoId;
        return $this;
    }
}
