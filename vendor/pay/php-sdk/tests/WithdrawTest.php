<?php

namespace PayCenter\Tests;

use PayCenter\Request\Withdraw\{AddFreeQuotaRequest, ApplyBailWithdrawRequest, ApplyResidueWithdrawRequest, ApplyWithdrawRequest, FeeRequest, FreeQuotaAddedLogRequest, InfoRequest, SystemApplyWithdrawRequest, ToWithdrawRequest};

class WithdrawTest extends TestCase
{
    /**
     * @param ApplyWithdrawRequest $req
     * @dataProvider applyRequestsProvider
     * @throws \PayCenter\Exception\Exception
     */
    public function testApply(ApplyWithdrawRequest $req)
    {
        $res = $req->request();
        $this->assertResponse($res);

        $req = new ToWithdrawRequest();
        $req->setOrderNo($res->getOrderNo())
            ->setStatus(ToWithdrawRequest::WITHDRAW_STATUS_APPROVE)
            ->setRemarks('unitTest')
            ->request();

        $this->assertRequest(new InfoRequest($res->getOrderNo()));
    }

    /**
     * @return array
     * @throws \PayCenter\Exception\ConfigException
     */
    public function applyRequestsProvider()
    {
        return [
            [(new ApplyWithdrawRequest())->setMoney(100)->setUserinfoId(2)->setIp('1.1.1.1')->setContent(['body' => 'test'])],
            [(new ApplyResidueWithdrawRequest())->setMoney(100)->setUserinfoId(2)->setIp('1.1.1.1')->setContent(['body' => 'test'])],
            [(new SystemApplyWithdrawRequest())->setMoney(100)->setUserinfoId(2)->setIp('1.1.1.1')->setContent(['body' => 'test'])->setBankCardJson('{"cardNo":"6214855713032825","idCode":"330327199102237230","accountName":"方高泽","ledgerno":"8d6efa7ecf577a35089c1b8affcb491b","accountTel":"15557590637","type":"DC","province_code":"330000","city_code":"330100","customertype":"PERSON","bank_code":"03080000","bankName":"招商银行","bankCode":"CMB"}')],
            [(new ApplyBailWithdrawRequest())->setMoney(100)->setUserinfoId(2)->setIp('1.1.1.1')->setContent(['body' => 'test'])]
        ];
    }

    /**
     * @throws \PayCenter\Exception\Exception
     */
    public function testFee()
    {
        $res = (new FeeRequest(2, 100))->request();

        $this->assertResponse($res);

        $beforeQuota = $res->getSurplusFreeQuota();

        //免费提现额度开启时
        if ($beforeQuota >= 0) {
            //增加免费提现额度
            (new AddFreeQuotaRequest(2, 100))->setRemarks('unitTest')();

            $res = (new FeeRequest(2, 100))->request();
            $this->assertEquals($beforeQuota + 100, $res->getSurplusFreeQuota());

            $items = (new FreeQuotaAddedLogRequest)->setUserinfoId(2)->request()->getItems();
            $this->assertEquals(100, array_shift($items)->quota);
        }
    }
}
