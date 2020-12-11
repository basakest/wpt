<?php

namespace PayCenter\Request\Withhold;

class ToUserRequest extends WithholdRequest
{
    const PATH = 'api/v1.0/withhold/to-user';

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
