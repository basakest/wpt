<?php

namespace PayCenter\Request\Refund;

use PayCenter\Request\Request;
use PayCenter\Request\ListRequestTrait;

class ListRequest extends Request
{
    use ListRequestTrait;

    const PATH = 'api/v1.0/refund/list';

    /**
     * 设置支付商户单号
     * @param array $outTradeNos
     * @return $this
     */
    public function setOutTradeNos(array $outTradeNos)
    {
        $this->outTradeNos = $outTradeNos;
        return $this;
    }

    /**
     * 增加支付商户单号
     * @param string $outTradeNo
     * @return $this
     */
    public function addOutTradeNo(string $outTradeNo)
    {
        $this->outTradeNos = array_unique(array_merge($this->outTradeNos ?? [], [$outTradeNo]));
        return $this;
    }
}
