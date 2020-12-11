<?php

namespace PayCenter\Tests;

use PayCenter\Request\Pay\PayReturnRequest;
use PayCenter\Request\Withhold\{ResidueRequest, ToBzjRequest, ToSystemRequest, ToUserRequest, ToUserResidueRequest, WithholdRequest};

class WithholdTest extends TestCase
{
    /**
     * @dataProvider requestsProvider
     * @param WithholdRequest $req
     * @return void
     * @throws \PayCenter\Exception\Exception
     */
    public function testWithhold(WithholdRequest $req)
    {
        $this->assertResponse($res = $req());
        //查询订单
        $this->assertResponse((new PayReturnRequest($res->getOrderNo()))());
    }

    /**
     * @return array
     * @throws \PayCenter\Exception\ConfigException
     */
    public function requestsProvider()
    {
        $content = ['body' => 'pay-php-sdk 测试', 'type' => 'sdk-test'];
        return [
            [(new ToBzjRequest())->setUserinfoId(2)->setMoney(100)->setContent($content)],
            [(new ToSystemRequest())->setUserinfoId(2)->setMoney(100)->setContent($content)],
            [(new ResidueRequest())->setToUserinfoId(1)->setUserinfoId(2)->setMoney(100)->setContent($content)],
            [(new ToUserRequest())->setToUserinfoId(3)->setUserinfoId(2)->setPayMethod(WithholdRequest::PAY_METHOD_RESIDUE)->setMoney(100)->setContent($content)],
            [(new ToUserResidueRequest())->setToUserinfoId(2)->setUserinfoId(3)->setPayMethod(WithholdRequest::PAY_METHOD_BALANCE)->setMoney(100)->setContent($content)],
            [(new ToBzjRequest())->setUserinfoId(8610718)->setMoney(100)->setPreferPayMethods(WithholdRequest::PAY_METHOD_BALANCE, WithholdRequest::PAY_METHOD_RESIDUE)->setContent($content)],
        ];
    }
}
