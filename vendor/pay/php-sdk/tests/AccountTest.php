<?php

namespace PayCenter\Tests;

use PayCenter\Request\Account\{AccountRequest, BalanceRequest, ListRequest, MultiListRequest, BusinessBalanceListRequest, BusinessBalanceDetailRequest, UpdateBusinessBalanceRequest};

class AccountTest extends TestCase
{
    /**
     * @dataProvider userinfoIdProvider
     * @param int $userinfoId
     * @return void
     * @throws \PayCenter\Exception\ConfigException
     */
    public function testBalance(int $userinfoId)
    {
        $this->assertTrue(is_numeric((new BalanceRequest($userinfoId))()));
    }

    /**
     * @dataProvider userinfoIdProvider
     * @param int $userinfoId
     * @return void
     * @throws \PayCenter\Exception\Exception
     */
    public function testList(int $userinfoId)
    {
        $accounts = (new ListRequest($userinfoId))();
        $this->assertNotEmpty($accounts);
        $this->assertTrue(is_array($accounts));

        $accounts = (new ListRequest($userinfoId))->setAccountType(ListRequest::BAIL_ACCOUNT, ListRequest::BALANCE_ACCOUNT)->request();
        $this->assertNotNull($accounts[ListRequest::BAIL_ACCOUNT]);
        $this->assertNotNull($accounts[ListRequest::BALANCE_ACCOUNT]);
    }

    public function testMultiList()
    {
        $userinfoIds = [2, 3, 4, 5];
        $res = (new MultiListRequest($userinfoIds))();
        foreach ($userinfoIds as $userinfoId) {
            $this->assertArrayHasKey($userinfoId, $res);
        }
    }

    /**
     * @dataProvider userinfoIdProvider
     * @param int $userinfoId
     * @return void
     * @throws \PayCenter\Exception\Exception
     */
    public function testBusinessBalance(int $userinfoId)
    {
        $businessBalanceListRequest = new BusinessBalanceListRequest($userinfoId);
        $businessBalanceListRequest->setPageSize(1)->setAccountType(BusinessBalanceListRequest::BALANCE_ACCOUNT);
        $response = $businessBalanceListRequest();
        $this->assertResponse($response);

        if ($response->items) {
            $item = $response->items[count($response->items)-1];
            (new BusinessBalanceDetailRequest($userinfoId, $item->id))();

            //修改 BusinessBalance 看看
            $req = new UpdateBusinessBalanceRequest($userinfoId);
            $req->setOutTradeNo($item->outTradeNo)
                ->setBalanceType($item->balanceType)
                ->setTarget('sale')
                ->setTargetId(123)
                ->setTargetUri('321')
                ->setBusinessType('fortest')
                ->setBalanceStatus(AccountRequest::BALANCE_STATUS_ERROR);
            $req();

            $item = (new BusinessBalanceDetailRequest($userinfoId, $item->id))();
            $this->assertEquals($req->target, $item->target);
            $this->assertEquals($req->targetId, $item->targetId);
            $this->assertEquals($req->targetUri, $item->targetUri);
            $this->assertEquals($req->businessType, $item->businessType);
            $this->assertEquals($req->balanceStatus, $item->balanceStatus);

            $req = new UpdateBusinessBalanceRequest($userinfoId);
            $req->setOrderNo($item->orderNo)
                ->setTarget('sale1')
                ->setTargetId(321)
                ->setTargetUri('123')
                ->setBusinessType('fortest1')
                ->setBalanceStatus(AccountRequest::BALANCE_STATUS_DEDUCT);
            $req();

            $item = (new BusinessBalanceDetailRequest($userinfoId))->setOrderNo($item->orderNo)();
            $this->assertEquals($req->target, $item->target);
            $this->assertEquals($req->targetId, $item->targetId);
            $this->assertEquals($req->targetUri, $item->targetUri);
            $this->assertEquals($req->businessType, $item->businessType);
            $this->assertEquals($req->balanceStatus, $item->balanceStatus);

            $res = (new BusinessBalanceListRequest($userinfoId))
                ->setTarget($item->target)
                ->setTargetUri($item->targetUri, $item->targetUri)
                ->setColumns('targetUri')
                ->setGroupBy('targetUri')
                ->request();
            $this->assertNotEmpty($res->getItems());
            $this->assertEquals($req->targetUri, $res->getItems()[0]->targetUri);
        }
    }

    public function userinfoIdProvider()
    {
        return [
            [2],
        ];
    }
}
