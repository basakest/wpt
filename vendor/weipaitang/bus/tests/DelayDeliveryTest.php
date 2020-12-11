<?php


namespace Tests;


use App\Logic\BuyerAuctionLogic;
use App\Logic\ExpressLogic;
use App\Logic\OrderLogic;
use App\Logic\SellerOrderLogic;
use phpDocumentor\Reflection\Types\This;
use PHPUnit\Framework\TestCase;
use WptBus\Facades\Bus;

class DelayDeliveryTest extends TestCase
{

    protected $config = [];

    protected $bus;
    public $orderLogic = null;
    public $buyerAuctionLogic = null;
    public $expressLogic = null;

    public $sellerOrderLogic = null;


    public function setUp()
    {
        defined('TRACE_NAME') || define('TRACE_NAME', 'api');
        defined('TRACE_HOST') || define('TRACE_HOST', ip2long('127.0.0.1'));
        defined('TRACE_ID') || define('TRACE_ID', md5(TRACE_NAME . TRACE_HOST . uniqid() . rand(100000, 999999)));
        $ip = "127.0.0.1";
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
        $this->sellerOrderLogic = new SellerOrderLogic();
        $this->orderLogic = new OrderLogic();
        $this->buyerAuctionLogic = new BuyerAuctionLogic();
        $this->expressLogic = new ExpressLogic();

        app()->instance('bus', $this->bus);
    }
    public function test_Remind_delivery()
    {
        $orderIds = [1972391777, 1972391778, 2041130152];

        $ret = $this->bus::order()->delivery->GetOrderRemindMap($orderIds);

        var_dump($ret);
    }

    public function test_change_delivery()
    {
        $res = $this->expressLogic->updateExpress(2041109069, "2002191513o6zblt", "tiantian", "7788877655444443");
        var_dump($res);
    }

    public function test_Buyer_remind_delivery()
    {
        $res = $this->buyerAuctionLogic->remindDelivery("2001141751anik2h", 8612265);

        var_dump($res);
    }

    public function test_Apply_delay_delivery()
    {
        $ret = $this->sellerOrderLogic->applyDeliveryDelay("2001141751anik2h", 8611342, ['delayDay' => 3, 'delayReason' => "测试延迟发货"]);

        var_dump($ret);

    }

    public function test_deal_delay_delivery()
    {
        $ret = $this->orderLogic->dealDeliveryDelay("2001141751anik2h", 8612265, 1);

        var_dump($ret);
    }

}