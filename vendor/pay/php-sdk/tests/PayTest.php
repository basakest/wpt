<?php

namespace PayCenter\Tests;

use PayCenter\Request\Pay\CloseOrderRequest;
use PayCenter\Request\Pay\PayQueryRequest;
use PayCenter\Request\Pay\UnifiedOrder\{BatchTransRequest,
    CombinePayRequest,
    PayToUserOnwayRequest,
    PayToUserResidueRequest,
    UnifiedOrderRequest,
    ResidueRequest,
    RechargeRequest,
    RechargeBzjRequest,
    RechargeBailRequest,
    PayToUserRequest,
    PayToSystemRequest};
use PayCenter\Response\PayReturnResponse;

class PayTest extends TestCase
{
    /**
     * @dataProvider unifiedOrderRequestsProvider
     * @param UnifiedOrderRequest $req
     * @return void
     * @throws \PayCenter\Exception\Exception
     */
    public function testUnifiedOrder(UnifiedOrderRequest $req)
    {
        $this->assertResponse($res = $req());
        //测试关闭订单
        (new CloseOrderRequest($res->orderNo))();
    }


    /**
     * 合并支付下单
     * @dataProvider combineUnifiedOrderRequestsProvider
     * @group combine
     * @param UnifiedOrderRequest $req
     * @return void
     * @throws \PayCenter\Exception\Exception
     */
    public function testCombineUnifiedOrder(UnifiedOrderRequest $req)
    {
        $this->assertResponse($res = $req());
       (new CloseOrderRequest($res->orderNo))();
    }

    /**
     * 合并支付成功查询
     * @group combine
     * @throws \PayCenter\Exception\ConfigException
     * @throws \PayCenter\Exception\Exception
     */
    public function testCombineQuery()
    {
        $response = (new PayQueryRequest('2003120942343v5w'))->request();
        self::assertEquals('2003120942343v5w',$response->getOrderNo());
        self::assertEquals('3',count($response->getSubOrders()));
    }

    /**
     * @return array
     * @throws \PayCenter\Exception\ConfigException
     */
    public function unifiedOrderRequestsProvider()
    {
        $notifyUrl = 'http://example.com';
        $contentJson = '{"body":"pay-php-sdk 测试", "type":"test"}';

        return [
            [(new PayToUserOnwayRequest())->setMoney(100)->setUserinfoId(2)->setContentJson($contentJson)->setToUserinfoId(2)->setNotifyUrl($notifyUrl)],
            [(new ResidueRequest())->setMoney(100)->setUserinfoId(2)->setContentJson($contentJson)->setToUserinfoId(8620068)->setNotifyUrl($notifyUrl)->addReceiverUserinfoId(1, 2, 3)],
            [(new PayToUserRequest())->setMoney(100)->setUserinfoId(2)->setContentJson($contentJson)->setToUserinfoId(8620068)->setNotifyUrl($notifyUrl)],
            [(new PayToUserResidueRequest())->setMoney(100)->setUserinfoId(2)->setContentJson($contentJson)->setToUserinfoId(8620068)->setNotifyUrl($notifyUrl)],
            [(new RechargeRequest())->setMoney(100)->setUserinfoId(2)->setContentJson($contentJson)->setNotifyUrl($notifyUrl)->setAppointPayMethodList([1,2,4,5])],
            [(new RechargeBzjRequest())->setMoney(100)->setUserinfoId(2)->setContentJson($contentJson)->setNotifyUrl($notifyUrl)->setIgnorePayMethodList([1,2,3])],
            [(new RechargeBailRequest())->setMoney(100)->setUserinfoId(2)->setContentJson($contentJson)->setNotifyUrl($notifyUrl)->setSubsidy(10)],
            [(new PayToSystemRequest())->setMoney(100)->setUserinfoId(2)->setContentJson($contentJson)->setNotifyUrl($notifyUrl)->setBusinessOrderNo('phpsdktest' . mt_rand(10000, 99999))],
            [(new BatchTransRequest())->setMoney(200)->setUserinfoId(2)->setContentJson($contentJson)->addReceipt(1, 100)->addReceipt(3, 100)],
            [(new BatchTransRequest())->setMoney(200)->setUserinfoId(2)->setContentJson($contentJson)->setReceipts([['userinfoId' => 1, 'money' => 100], ['userinfoId' => 3, 'money' => 100]])],
        ];
    }

    /**
     * @return array
     * @throws \PayCenter\Exception\ConfigException
     */
    public function combineUnifiedOrderRequestsProvider()
    {
        $notifyUrl = 'http://api-in-vpc.wptqctest.com/pay/v1.0/imusertransfer/notify';
        $contentJson = '{"total_fee":"9800","type":"combine","fromUserinfoUri":"1912241245gZegeW","fromNickname":"清荷文玩","fromHeadimgurl":"http://cdn.weipaitang.com/certify/20200109aofpz1aw-rtcz-qtw0-ouea-i1givkuefznz-W419H419/0","fromVerifyType":"individual","toUserinfoUri":"1807152129kNwtNV","toNickname":"唯吾知足～长乐","toHeadimgurl":"http://appwpt-10002380.image.myqcloud.com/20181104c5955ff2-abe9-42ac-9426-54096034aef4","toVerifyType":"","channelId":"C20200310075938JXDPY06NP069JHT4A","staffId":"11852213","device":"app","userType":"staff","remark":"","tag":[],"body":"合并支付 - 98 元"}';
        $subOrders = [
            ['userinfoId'=>8622011,'money'=>2900,'subsidy'=>90,'bizSubOrderNo'=>$this->createId()],
            ['userinfoId'=>8622010,'money'=>3400,'subsidy'=>190,'bizSubOrderNo'=>$this->createId()],
            ['userinfoId'=>8622009,'money'=>3500,'subsidy'=>270,'bizSubOrderNo'=>$this->createId()]
        ];

        return [
            [(new CombinePayRequest())->setMoney(9800)
                ->setUserinfoId(8622012)
                ->setContent($contentJson)
                ->setSubsidy(550)
                ->setSubOrders($subOrders)
                ->setNotifyUrl($notifyUrl)
                ->setReturnUrl($notifyUrl)
                ->setBusinessOrderNo($this->createId())]
        ];
    }
}
