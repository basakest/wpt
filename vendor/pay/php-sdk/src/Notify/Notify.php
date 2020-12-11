<?php

namespace PayCenter\Notify;

use PayCenter\Exception\ResponseException;
use PayCenter\Response\Response;

abstract class Notify extends Response
{
    /**
     * 解析原始数据
     * @throws ResponseException
     */
    protected function parseOriginal()
    {
        $this->data = json_decode($this->original);
        if (empty($this->data)) {
            throw new ResponseException('支付中心回调通知数据异常', $this);
        }
    }
}
