<?php
/**
 * Created by PhpStorm.
 * User: fangchaogang
 * Date: 2019-04-01
 * Time: 10:25
 */
namespace PayCenter\Response\CreditPay;
use PayCenter\Response\Response;

class CompleteOrderResponse extends Response
{


    public function getResourceData()
    {
        return $this->resourceData;
    }
}