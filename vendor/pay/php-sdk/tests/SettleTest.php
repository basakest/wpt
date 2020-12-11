<?php

namespace PayCenter\Tests;

use PayCenter\Request\Pay\GetPrepayIdRequest;
use PayCenter\Request\Pay\PayQueryRequest;
use PayCenter\Request\Pay\UnifiedOrder\CombinePayRequest;
use PayCenter\Request\Settle\SettleRequest;
use PayCenter\Request\Settle\ToBalanceRequest;
use PayCenter\Request\Settle\ToResidueRequest;
use PayCenter\Request\Settle\ToSystemAdvanceRequest;
use PayCenter\Request\Withhold\ResidueRequest;

class SettleTest extends TestCase
{
    const PAY_USERINFOID = 2;
    const PAY_MONEY = 100;
    const TO_USERINFOID = 8610718;

    /**
     * @dataProvider requestsProvider
     * @param SettleRequest $req
     * @return void
     * @throws \PayCenter\Exception\Exception
     */
    public function testNormal(SettleRequest $req)
    {
        $orderNo = $this->pay()->getOrderNo();
        sleep(1); //等待支付异步处理结束
        $req->setBusinessOrderNo(rand())
            ->setOrderNo($orderNo)
            ->setMoney(self::PAY_MONEY)
            ->setFee(10);
        $this->assertRequest($req);
        //再请求一次返回结果相同
        $this->assertRequest($req);
    }

    /**
     * @dataProvider requestsProvider
     * @param SettleRequest $req
     * @return void
     * @throws \PayCenter\Exception\Exception
     */
    public function testMulti(SettleRequest $req)
    {
        //支付多笔
        $outTradeNo1 = $this->pay()->getOutTradeNo();
        $outTradeNo2 = $this->pay()->getOutTradeNo();
        sleep(1); //等待支付异步处理结束
        $req->setBusinessOrderNo(rand())
            ->setOutTradeNo($outTradeNo1)
            ->setMultiOutTradeNo([$outTradeNo1, $outTradeNo2])
            ->setMoney(self::PAY_MONEY * 2)
            ->setFee(10);
        $this->assertRequest($req);
    }

    /**
     * @dataProvider requestsProvider
     * @param SettleRequest $req
     * @return void
     * @throws \PayCenter\Exception\Exception
     */
    public function testCombine(SettleRequest $req)
    {
        //合并付款暂未开放结算到系统
        if ($req instanceof ToSystemAdvanceRequest) {
            return;
        }

        $combinePayReq = new CombinePayRequest();
        $combinePayReq->setMoney(self::PAY_MONEY)
                      ->setUserinfoId(self::PAY_USERINFOID)
                      ->setSubOrders([["userinfoId" => self::TO_USERINFOID, "money" => self::PAY_MONEY / 2, "bizSubOrderNo" => rand(), 'subsidy' => 0], ["userinfoId" => self::TO_USERINFOID, "money" => self::PAY_MONEY / 2, 'subsidy' => 0, "bizSubOrderNo" => rand()]])
                      ->setContent(['type' => 'residue', 'body' => '支付测试'])
                      ->setBusinessOrderNo(rand());

        //合并支付订单号
        $combinePayOrderNo = $combinePayReq->request()->getOrderNo();

        //余额支付
        $payReq = new GetPrepayIdRequest();
        $payReq->orderNo = $combinePayOrderNo;
        $payReq->payMethod = 1;
        $payReq->tradePassword = md5(md5('111111') . $combinePayOrderNo);
        $this->assertRequest($payReq);

        //查询支付结果
        $payQueryReq = new PayQueryRequest($combinePayOrderNo);
        $subOrders = $payQueryReq()->subOrders;

        //合并支付结算
        foreach ($subOrders as $subOrder) {
            $req->setBusinessOrderNo(rand())
                ->setOrderNo($combinePayOrderNo)
                ->setSubOutTradeNo($subOrder->subOutTradeNo)
                ->setMoney(self::PAY_MONEY / 2)
                ->setFee(10);
            $this->assertRequest($req);
        }
    }

    protected function pay()
    {
        $req = (new ResidueRequest())
            ->setMoney(self::PAY_MONEY)
            ->setUserinfoId(self::PAY_USERINFOID)
            ->setToUserinfoId(self::TO_USERINFOID)
            ->setContent(['type' => 'residue', 'body' => '支付测试']);
        return $req->request();
    }

    public function requestsProvider()
    {
        $content = ['type' => 'order_settle', 'body' => '订单结算'];
        return [
            [(new ToBalanceRequest())->setToUserinfoId(self::TO_USERINFOID)->setContent($content)],
            [(new ToResidueRequest())->setToUserinfoId(self::TO_USERINFOID)->setContent($content)],
            [(new ToSystemAdvanceRequest())->setContent($content)],
            [(new ToResidueRequest())->setToUserinfoId(self::TO_USERINFOID)->setContent($content)->addProductProfitSharing(0,10)->addUserProfitSharing(1, 10)],
        ];
    }
}
