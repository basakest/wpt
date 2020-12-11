<?php


namespace Tests;


use App\Services\Order\OrderService;
use DeepCopy\f001\B;
use PHPUnit\Framework\TestCase;
use WptBus\Facades\Bus;

class OrederRefundTest extends TestCase
{
    protected $bus;

    public function setUp()
    {
        $ip = "172.16.32.84";
        $this->config = [
            'order' => [ // 服务名
                'http' => [ // http配置
                    'servers' => [
                        "http://$ip:8080/",
                    ]
                ]
            ]
        ];

        $this->bus = new \WptBus\Bus($this->config);
        app()->instance('bus', $this->bus);
    }


    public function test_apply_order_refund ()
    {
        $res = Bus::order()->refund->applyOrderRefund("20052115060j9793",
            8700102, 10000, 2, "未按约定时间发货");

        var_dump($res);
    }

    public function test_cancel_order_refund()
    {
        $res = Bus::order()->refund->cancelOrderRefund("20052115060j9793", 8700102);
        var_dump($res);
    }


    public function test_get_oreder_refund()
    {
        $res = Bus::order()->refund->getOrderRefund(0, "20052115060j9793",2, ["id", "orderId", "refundStatus"]);
        var_dump($res);
    }

    public function test_refund_order()
    {
        $res = Bus::order()->refund->refundOrder(2041170897, 8620155, "self");
        var_dump($res);
    }


    public function test_has_orderRefund()
    {
        $ret = OrderService::getInstance()->hasOrderRefund("", 2041170897);
        var_dump($ret);
    }


    public function testGetOrderListByPid()
    {
        $fields = ["saleId", "paidTime", "saleType", "winJson"];
        $saleId = 1972390495;
        $pageId = 1;
        $pageSize = 20;
        $ret =Bus::order()->order->getRelationOrderList(1972390495, $fields, $pageId, $pageSize);

        var_dump($ret);
    }

    public function testGetOrderRefundReason()
    {
        $ret = Bus::order()->orderReturn->getOrderRefundApplyReason(2041170897);
        var_dump($ret);
    }

}