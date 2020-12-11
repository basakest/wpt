<?php

namespace PayCenter\Tests;
use PayCenter\Request\Bankcard\QueryListRequest;
use PayCenter\Request\Bankcard\QueryRequest;
use PayCenter\Request\Bankcard\ToLastUseTimeRequest;
use PayCenter\Request\Transaction\OnwayToBalanceRequest;

class BankcardTest extends TestCase
{
    /**
     * @dataProvider queryProvider
     * @param $userinfId
     * @param $bankCardId
     * @throws \PayCenter\Exception\ConfigException
     * @throws \PayCenter\Exception\Exception
     */
    public function testQuery($userinfId, $bankCardId)
    {
        $req = (new QueryRequest())->setUserinfoId($userinfId)->setBankCardId($bankCardId);
        $respone = $this->assertResponse($req());
    }

    /**
     * @throws \PayCenter\Exception\ConfigException
     * @throws \PayCenter\Exception\Exception
     */
    public function testQueryList()
    {
        $userinfoIds = [8620068,2];
        $cardNos = ["6217001440001734459"];
        $reqUserinfoIds = (new  QueryListRequest())->setUserinfoIds($userinfoIds);
        $this->assertResponse($reqUserinfoIds());

        $reqCardNos = (new QueryListRequest())->setCardNos($cardNos);
        $this->assertResponse($reqCardNos());

    }

    public function testUpdateLastUseTime()
    {
        $userinfoId = 8620068;
        $bankCardId = 366812;
        $req = (new ToLastUseTimeRequest())->setUserinfoId($userinfoId)->setBankCardId($bankCardId)->setLastUseTime(time());
        $this->assertResponse($req());
    }

//    public function testOther()
//    {
//        $req = (new RedpacketReceiptBalanceRequest())->setOutTradeNo('19062515323vdlfz')
//            ->setBusinessOrderNo(date('Ymd').rand(100,999))
//            ->setContentJson('{"body":"test"}')
//            ->setToUserinfoId(8620068)
//            ->setMoney(1000);
//        $this->assertResponse($req());
//
//    }
    public function queryProvider()
    {
        return [
            [8620068,366812],
        ];
    }
}
