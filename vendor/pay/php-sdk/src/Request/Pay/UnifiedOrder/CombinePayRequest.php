<?php

namespace PayCenter\Request\Pay\UnifiedOrder;

/**
 * 合并支付
 * Class CombinePayRequest
 * @package PayCenter\Request\Pay\UnifiedOrder
 */
class CombinePayRequest extends UnifiedOrderRequest
{
    const PATH = 'api/v1.0/pay/combine-pay';

    /**
     * 子订单号列表
     * @param array $subOrders
     * @return static
     */
    public function setSubOrders(array $subOrders): self
    {
        $this->subOrders = $subOrders;
        return $this;
    }
}
