<?php

namespace PayCenter\Request\Pay;

use PayCenter\Request\Request;

class CloseOrderRequest extends Request
{
    const PATH = 'api/v1.0/pay/close-order';

    public function __construct(string $orderNo)
    {
        parent::__construct();
        $this->orderNo = $orderNo;
    }
}
