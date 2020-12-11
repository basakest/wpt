<?php

namespace PayCenter\Request\Transfer;

trait SetOriginsTrait
{
    /**
     * 设置转账来源订单
     * @param array $origins
     * @return $this
     */
    public function setOrigins(array $origins)
    {
        $this->origins = $origins;
        return $this;
    }

    /**
     * 增加转账来源订单
     * @param string $orderNo
     * @param integer $money
     * @return $this
     */
    public function addOrigin(string $orderNo, int $money)
    {
        $this->origins = array_merge($this->origins ?? [], [$orderNo => $money]);
        return $this;
    }
}
