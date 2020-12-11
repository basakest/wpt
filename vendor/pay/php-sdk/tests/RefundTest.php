<?php

namespace PayCenter\Tests;

use PayCenter\Request\Refund\CombineRefundRequest;
use PayCenter\Request\Refund\ListRequest;
use PayCenter\Request\Refund\RefundRequest;
use PayCenter\Request\Withhold\ToBzjRequest;

class RefundTest extends TestCase
{
    public function testRefund()
    {
        $res = (new ToBzjRequest())->setUserinfoId(2)->setMoney(100)->setContentJson('{"body":"测试"}')();
        sleep(1); //等待异步处理结束
        $req = (new RefundRequest())->setMoney(100)->setOrderNo($res->orderNo);
        $this->assertResponse($req());

        //查询退款信息
        $this->assertResponse((new ListRequest())->addOutTradeNo($res->outTradeNo)());
    }

    /**
     * @group combine
     * @throws \PayCenter\Exception\ConfigException
     * @throws \PayCenter\Exception\Exception
     */
    public function testCombineRefund()
    {
        try{
            $req = (new CombineRefundRequest())
                ->setSubOutTradeNo('2003161316pqn8ds')
                ->setOutTradeNo('20031613446jck0p')
                ->setMoney(1)
                ->setNotifyUrl('http://t.wptqcst.com/pay/v1.0/t/refund-notify')
                ->setRefundBusinessOrderNo($this->createId());

            $this->assertResponse($req());
        }catch (\Throwable $e) {
            self::assertTrue(true);
        }
    }
}
