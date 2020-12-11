<?php

namespace PayCenter\Tests;

use PayCenter\Request\Transfer\{
    BailToSystemBailRequest,
    SystemBailToBalanceRequest,
    BailToSystemOnwayRequest,
    BalanceToSystemOnwayRequest,
    SystemDeductBailRequest,
    SystemDeductBzjRequest,
    SystemDeductRequest,
    SystemDeductResidueRequest,
    SystemFrozenBailRequest,
    SystemFrozenRequest,
    SystemFrozenResidueRequest,
    SystemRechargeBailRequest,
    SystemRechargeRequest,
    SystemRechargeResidueRequest,
    TransferForBailRequest,
    TransferForBalanceRequest,
    TransferRequest,
    SystemBalanceToOnwayRequest,
    SystemOnwayToBalanceRequest,
    SystemOnwayToBailRequest,
    OnwayToResidueRequest,
    ResidueToOnwayRequest,
    ResidueToSystemAdvanceRequest,
    ResidueToSystemOnwayRequest,
    SystemAdvanceToResidueRequest,
    ThawRequest,
    SystemBailToBailRequest
};

class TransferTest extends TestCase
{
    /**
     * @dataProvider systemRechargeRequestsProvider
     * @param SystemRechargeRequest $req
     * @return void
     * @throws \PayCenter\Exception\Exception
     */
    public function testSystemRecharge(SystemRechargeRequest $req)
    {
        $this->assertResponse($req());
    }

    /**
     * @return array
     * @throws \PayCenter\Exception\ConfigException
     */
    public function systemRechargeRequestsProvider()
    {
        return [
            [new SystemRechargeRequest(2, 2000)],
            [new SystemRechargeBailRequest(2, 2000)],
            [new SystemRechargeResidueRequest(2, 2000)],
        ];
    }

    /**
     * @dataProvider systemDeductRequestsProvider
     * @param SystemDeductRequest $req
     * @return void
     * @throws \PayCenter\Exception\Exception
     */
    public function testSystemDeduct(SystemDeductRequest $req)
    {
        $this->assertResponse($req());
    }

    /**
     * @return array
     * @throws \PayCenter\Exception\ConfigException
     */
    public function systemDeductRequestsProvider()
    {
        return [
            [new SystemDeductRequest(2, 200)],
            [new SystemDeductBailRequest(2, 200)],
            [new SystemDeductResidueRequest(2, 200)],
            [new SystemDeductBzjRequest(2, 100)],
        ];
    }

    /**
     * @dataProvider systemFrozenRequestsProvider
     * @param SystemFrozenRequest $req
     * @return void
     * @throws \PayCenter\Exception\Exception
     */
    public function testSystemFrozen(SystemFrozenRequest $req)
    {
        $this->assertResponse($res = $req->request());

        $thawRequest = new ThawRequest();
        $thawRequest->setOutTradeNo($res->getOutTradeNo())
                    ->setMoney($req->money)
                    ->setContent(['body' => '解冻'])
                    ->setBusinessOrderNo((string)rand());
        $this->assertResponse($thawRequest());
    }

    /**
     * @return array
     * @throws \PayCenter\Exception\ConfigException
     */
    public function systemFrozenRequestsProvider()
    {
        return [
            [new SystemFrozenRequest(2, 200)],
            [new SystemFrozenResidueRequest(2, 200)],
            [new SystemFrozenBailRequest(2, 200)],
        ];
    }

    /**
     * @dataProvider transferRequestsProvider
     * @param TransferRequest $req
     * @return void
     * @throws \PayCenter\Exception\Exception
     */
    public function testTransfer(TransferRequest $req)
    {
        $this->assertResponse($req());
    }

    /**
     * @return array
     * @throws \PayCenter\Exception\ConfigException
     */
    public function transferRequestsProvider()
    {
        return [
            [new TransferForBalanceRequest(2, 1, 1)],
            [new TransferForBailRequest(2, 1, 1)],
            [(new SystemBalanceToOnwayRequest())->setMoney(100)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand())],
            [(new SystemBalanceToOnwayRequest())->setToUserinfoId(2)->setMoney(200)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand())],
            [(new SystemOnwayToBalanceRequest())->setToUserinfoId(2)->setMoney(200)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand())],
            [(new SystemOnwayToBailRequest())->setToUserinfoId(2)->setMoney(200)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand())],
            [(new OnwayToResidueRequest())->setUserinfoId(2)->setToUserinfoId(1)->setMoney(1)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand())],
            [(new ResidueToOnwayRequest())->setUserinfoId(1)->setToUserinfoId(2)->setMoney(1)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand())],
            [(new BalanceToSystemOnwayRequest())->setUserinfoId(2)->setMoney(1)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand())],
            [(new BailToSystemOnwayRequest())->setUserinfoId(2)->setMoney(1)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand())],
            [(new ResidueToSystemOnwayRequest())->setUserinfoId(2)->setMoney(1)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand())],
            [(new SystemAdvanceToResidueRequest())->setToUserinfoId(2)->setMoney(1)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand())],
            [(new ResidueToSystemAdvanceRequest())->setUserinfoId(2)->setMoney(1)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand())],
        ];
    }

    public function testSystemBail()
    {
        $systemBailToBalanceRequest = (new SystemBailToBalanceRequest())->setToUserinfoId(2)->setMoney(2)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand());
        $bailToSystemBailRequest = (new BailToSystemBailRequest())->setUserinfoId(2)->setMoney(1)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand());
        $systemBailToBalanceRequest->addOrigin($bailToSystemBailRequest->request()->getOrderNo(), 1);
        $bailToSystemBailRequest = (new BailToSystemBailRequest())->setUserinfoId(2)->setMoney(1)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand());
        $systemBailToBalanceRequest->addOrigin($bailToSystemBailRequest->request()->getOrderNo(), 1);
        $this->assertResponse($systemBailToBalanceRequest());

        $systemBailToBalanceRequest = (new SystemBailToBalanceRequest())->setToUserinfoId(2)->setMoney(3)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand());
        $bailToSystemBailRequest1 = (new BailToSystemBailRequest())->setUserinfoId(2)->setMoney(2)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand());
        $bailToSystemBailRequest2 = (new BailToSystemBailRequest())->setUserinfoId(2)->setMoney(2)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand());
        $systemBailToBalanceRequest->setOrigins([$bailToSystemBailRequest1->request()->getOrderNo() => $bailToSystemBailRequest1->money, $bailToSystemBailRequest2->request()->getOrderNo() => $bailToSystemBailRequest2->money]);
        $this->assertResponse($systemBailToBalanceRequest());

        $systemBailToBailRequest = (new SystemBailToBailRequest())->setToUserinfoId(2)->setMoney(3)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand());
        $bailToSystemBailRequest1 = (new BailToSystemBailRequest())->setUserinfoId(2)->setMoney(2)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand());
        $bailToSystemBailRequest2 = (new BailToSystemBailRequest())->setUserinfoId(2)->setMoney(2)->setContent(['type' => 'test'])->setBusinessOrderNo((string)rand());
        $systemBailToBailRequest->setOrigins([$bailToSystemBailRequest1->request()->getOrderNo() => $bailToSystemBailRequest1->money, $bailToSystemBailRequest2->request()->getOrderNo() => $bailToSystemBailRequest2->money]);
        $this->assertResponse($systemBailToBailRequest());
    }
}
