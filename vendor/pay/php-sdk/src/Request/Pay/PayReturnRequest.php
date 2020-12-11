<?php

namespace PayCenter\Request\Pay;

use PayCenter\Request\Request;
use PayCenter\Response\PayReturnResponse;

class PayReturnRequest extends Request
{
    const PATH = 'api/v1.0/pay/pay-return';

    public function __construct(string $orderNo)
    {
        parent::__construct();
        $this->orderNo = $orderNo;
    }

    public function request()
    {
        return new PayReturnResponse(parent::request());
    }
}
