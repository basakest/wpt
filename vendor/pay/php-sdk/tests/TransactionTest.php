<?php


namespace PayCenter\Tests;


use PayCenter\Request\Transaction\CombineReceiptFrozenRequest;
use PayCenter\Request\Transaction\CombineReceiptResidueRequest;
use PayCenter\Request\Transaction\ReceiptToSystemAdvanceRequest;
use PayCenter\Request\Transaction\ReceiptBalanceRequest;
use PayCenter\Request\Transaction\ReceiptFrozenRequest;
use PayCenter\Request\Transaction\ReceiptToRefundRequest;
use PayCenter\Request\Withhold\ResidueRequest;

class TransactionTest extends TestCase
{
    /**
     * @group combine
     * @throws \PayCenter\Exception\ConfigException
     * @throws \PayCenter\Exception\Exception
     */
    public function testCombineReceiptResidue()
    {
        try{
            $rs = (new CombineReceiptResidueRequest())->setBusinessOrderNo($this->createId())
                ->setMoney(10)
                ->setFee(0)
                ->setContentJson('{"body":"货款到账 - 29.80元","out_trade_no":"2003101737j9cvfc","cardType":"OTHERS","target":"sale","targetId":2072729158,"targetUri":"2003090449n32rxx","type":"residue","isNewPay":true,"feeJson":{"live":"直播拍手续费 - 0.50元"},"commissionJson":{"live":{"fee":50,"desc":"直播拍手续费","rate":3}}}')
                ->setToUserinfoId(8622011)
                ->setSubOutTradeNo('20031613168z0wio')
                ->setOutTradeNo('20031613446jck0p')
                ->request();
            self::assertEquals($rs->getSubOutTradeNo(),'20031613168z0wio');
        }catch (\Throwable $e){
            self::assertContains('金额错误',$e->getMessage());
        }
    }

    /**
     * @group combine
     * @throws \PayCenter\Exception\ConfigException
     * @throws \PayCenter\Exception\Exception
     */
    public function testCombineFrozenResidue()
    {
        try{
            $rs = (new CombineReceiptFrozenRequest())->setBusinessOrderNo($this->createId())
                ->setMoney(2)
                ->setFee(1)
                ->setContentJson('{"body":"货款到账 - 29.80元","out_trade_no":"2003101737j9cvfc","cardType":"OTHERS","target":"sale","targetId":2072729158,"targetUri":"2003090449n32rxx","type":"residue","isNewPay":true,"feeJson":{"live":"直播拍手续费 - 0.50元"},"commissionJson":{"live":{"fee":50,"desc":"直播拍手续费","rate":3}}}')
                ->setToUserinfoId(8622010)
                ->setSubOutTradeNo('2003161316uxgrsj')
                ->setOutTradeNo('20031613446jck0p')
                ->request();
            self::assertEquals($rs->getSubOutTradeNo(),'2003161316uxgrsj');
        }catch (\Throwable $e){
            self::assertContains('金额错误',$e->getMessage());
        }
    }

    public function testReceiptBalance()
    {
        //1、代扣货款（直接集成了下单、支付流程）
        $userinfoId = 2;
        $toUserinfoId = 8620068;
        $money = 100;
        $notifyUrl = 'http://phpunit.dev.com';
        $contentJson = '{"body":"residuePHPUNIT货款代扣"}';
        $req = (new ResidueRequest())->setMoney($money)
                ->setUserinfoId($userinfoId)
                ->setToUserinfoId($toUserinfoId)
                ->setNotifyUrl($notifyUrl)
                ->setContentJson($contentJson);
        $respone = $this->assertResponse($req());

        sleep(1);

        //2、确认收货到余额（确认收货到余额是不可以退款的）
        $contentJson = '{"body":"receipt PHPUNIT确认收货"}';
        $req = (new ReceiptBalanceRequest())->setMoney($money)
                ->setContentJson($contentJson)
                ->setOutTradeNo($respone->outTradeNo)
                ->setBusinessOrderNo($this->_buildOrderNo('B'));
        $respone = $this->assertResponse($req());
    }

    public function testReceiptFrozen()
    {
        //1、代扣货款（直接集成了下单、支付流程）
        $userinfoId = 2;
        $toUserinfoId = 8620068;
        $money = 100;
        $notifyUrl = 'http://phpunit.dev.com';
        $contentJson = '{"body":"residuePHPUNIT货款代扣"}';
        $req = (new ResidueRequest())->setMoney($money)
                ->setUserinfoId($userinfoId)
                ->setToUserinfoId($toUserinfoId)
                ->setNotifyUrl($notifyUrl)
                ->setContentJson($contentJson);
        $respone = $this->assertResponse($req());

        sleep(1);

        //2、确认收货到冻结余额（确认收货到余额是不可以退款的）
        $contentJson = '{"body":"receipt PHPUNIT确认收货"}';
        $req = (new ReceiptFrozenRequest())->setMoney($money)
                ->setContentJson($contentJson)
                ->setOutTradeNo($respone->outTradeNo)
                ->setBusinessOrderNo($this->_buildOrderNo('B'));
        $respone = $this->assertResponse($req());

        //3、申请退款
        $contentJson = '{"body":"receipt PHPUNIT确认收货退款"}';
        $req = (new ReceiptToRefundRequest())->setMoney($money)
                ->setContentJson($contentJson)
                ->setOutTradeNo($respone->outTradeNo)
                ->setRefundBusinessOrderNo($this->_buildOrderNo('RR'))
                ->setRefundNo($respone->outTradeNo)
                ->setNotifyUrl($notifyUrl);
        $this->assertResponse($req());
    }

    public function testReceiptToSystemAdvance()
    {
        //1、代扣货款（直接集成了下单、支付流程）
        $userinfoId = 2;
        $toUserinfoId = 8620068;
        $money = 100;
        $notifyUrl = 'http://phpunit.dev.com';
        $contentJson = '{"body":"residuePHPUNIT货款代扣"}';
        $req = (new ResidueRequest())->setMoney($money)
                ->setUserinfoId($userinfoId)
                ->setToUserinfoId($toUserinfoId)
                ->setNotifyUrl($notifyUrl)
                ->setContentJson($contentJson);
        $respone = $this->assertResponse($req());

        sleep(1);

        //2、确认收货到余额（确认收货到余额是不可以退款的）
        $contentJson = '{"body":"receipt PHPUNIT确认收货"}';
        $req = (new ReceiptToSystemAdvanceRequest())->setMoney($money)
                ->setContentJson($contentJson)
                ->setOutTradeNo($respone->outTradeNo)
                ->setBusinessOrderNo($this->_buildOrderNo('B'));
        $respone = $this->assertResponse($req());
    }

    private function _buildOrderNo(string $prefix = 'R')
    {
        return 'phpunit'.$prefix.date('YmdHis').rand(100,900);
    }
}
