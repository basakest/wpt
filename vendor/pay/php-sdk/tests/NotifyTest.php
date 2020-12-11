<?php

namespace PayCenter\Tests;

use PayCenter\Notify\{PayNotify, RefundNotify, WithdrawNotify};

class NotifyTest extends TestCase
{
    /**
     * @dataProvider notifyContentProvider
     * @return void
     */
    public function testNotify($notifyClass, $content)
    {
        $this->assertResponse(new $notifyClass($content));
    }

    public function notifyContentProvider()
    {
        return [
            [PayNotify::class, '{"code":0,"userinfoId":8613001,"outTradeNo":"181226182897zf9z","orderNo":"18122618282zhk5j","businessOrderNo":"18122618282zhk5j","resourceData":{"code":0,"synchronize":1,"data":{"msg":"余额支付成功"},"out_trade_no":"181226182897zf9z","money":1000,"bnp":1},"openid":"","totalFee":1000,"fee":0,"payMethod":1,"businessPayMethod":"balance","bankCardJson":"","cardType":"balance","attach":"","paidTime":1545820115,"payUserinfoId":8613001,"platform":2,"product":1,"signature":"A94BEAD0B010E15A5C5F831451379E50"}'],
            [RefundNotify::class, '{"outTradeNo":"1907041215jpdd8b","code":0,"resourceData":{"msg":"ok"},"totalFee":100,"refundMoney":100,"payMoney":100,"subsidy":0,"fee":0,"outRefundNo":"1907041215xf93zs","refundBusinessOrderNo":"phpunitRR20190704041510374","payMethod":1,"businessPayMethod":"balance","product":1,"signature":"E2D9E06EDAB46EB55C6B829F738DB3B2"}'],
            [WithdrawNotify::class, '{"code":0,"status":3,"product":1,"withdrawMethod":"2","outTradeNo":"1907031526d5cczp","orderNo":"1907031526d5cczp","businessOrderNo":"1907031526d5cczp","actualMoney":10780,"money":11000,"fee":220,"remarks":"OK","accountType":1,"payMethod":4,"bankCardJson":null,"userinfoId":8610718,"userIdentifier":"8610718","createTime":1562138802,"signature":"6A2CF839C8FC612E08FDE5B414466B4D"}']
        ];
    }
}
