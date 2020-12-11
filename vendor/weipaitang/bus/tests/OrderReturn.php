<?php


namespace Tests;

use App\ConstDir\ErrorConst;
use App\Exceptions\ApiException;
use App\Facades\Sale\SaleExtend;
use App\Logic\ExpressLogic;
use App\Logic\OrderLogic;
use App\Models\OrderModel;
use App\Models\OrderPrepaymentModel;
use App\Services\Order\DashboardReturnService;
use App\Services\Order\OrderService;
use App\Services\Order\ReturnService;
use App\Utils\CommonUtil;
use App\Utils\ImageUtil;
use Illuminate\Database\Connection;
use PHPUnit\Framework\TestCase;
use UserCenterClient\UserAddress;
use WptBus\Facades\Bus;
use WptBus\Lib\DataCompare;
use WptBus\Lib\Utils;
use WptOrder\OrderService\Consts\OrderStatus;
use WptOrder\OrderService\Facades\Order;
use WptUtils\Str;

class OrderReturn extends TestCase
{
    protected $config = [];

    protected $bus;

    public function setUp()
    {
        defined('TRACE_NAME') || define('TRACE_NAME', 'api');
        defined('TRACE_HOST') || define('TRACE_HOST', ip2long('127.0.0.1'));
        defined('TRACE_ID') || define('TRACE_ID', md5(TRACE_NAME . TRACE_HOST . uniqid() . rand(100000, 999999)));
        // $ip = swoole_get_local_ip();
        $ip = "172.16.34.198";

        // $ip = $ip['en0'] ?? '';
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

    public function a()
    {
        return [1,2];
    }
    public function testApplyReturn()
    {
        // list($a,$b) = $this->a();
        // dd($a,$b);
        $columns = [
            'id',
            'type',
            'uri',
            'isDel',
            'userinfoId',
            'status',
            'enableReturn',
            'winUserinfoId',
            'profileJson',
            'endTime',
            'deliveryTime',
            'dispute',
            'winJson',
            'createTime',
            'finishedTime',
            'delayReceiptTime',
            'paidTime',
            'isRated',
            'afterSaleJson',
            'disputeJson'
        ];
        $saleRow =  \App\Facades\Sale\Sale::getSaleExtend(1972391302, $columns);
        $returnSerivce = new ReturnService();
        //dd(convert2cents(50));
        $returnSerivce->buyerApplyReturn($saleRow, 2 ,3 ,"test", "", 1000);
        // $ret = $this->bus->order()->orderReturn->applyReturn($saleRow->uri, 24,1, "12", 1, 1341, "");
        //  dd($ret);

    }


    public function testAgrreReturn()
    {
        $columns = [
            'id',
            'type',
            'uri',
            'isDel',
            'userinfoId',
            'status',
            'enableReturn',
            'winUserinfoId',
            'profileJson',
            'endTime',
            'deliveryTime',
            'dispute',
            'winJson',
            'createTime',
            'finishedTime',
            'delayReceiptTime',
            'paidTime',
            'isRated',
            'afterSaleJson',
            'disputeJson'
        ];
        $saleRow =  \App\Facades\Sale\Sale::getSaleExtend(1972391302, $columns);
        $returnSerivce = new ReturnService();
        $returnSerivce->userAgreeReturn(1972391302, 8700506, '{"addressCitySecondStageName":"北京市","addressCountiesThirdStageName":"东城区","addressDetailInfo":"女孩没吃过","addressPostalCode":"100010","err_msg":"edit_address:ok","proviceFirstStageName":"北京市","telNumber":"18245645455","userName":"通过地方"}');
    }

    public function testRejectReturn()
    {
        $columns = [
            'id',
            'type',
            'uri',
            'isDel',
            'userinfoId',
            'status',
            'enableReturn',
            'winUserinfoId',
            'profileJson',
            'endTime',
            'deliveryTime',
            'dispute',
            'winJson',
            'createTime',
            'finishedTime',
            'delayReceiptTime',
            'paidTime',
            'isRated',
            'afterSaleJson',
            'disputeJson'
        ];
        $saleRow =  \App\Facades\Sale\Sale::getSaleExtend(2142394016, $columns);
        $returnSerivce = new ReturnService();
        $returnSerivce->rejectReturn($saleRow, 14291365, 2, "dsdf", "");
    }

    public function testGetReturn()
    {
        $returnSerivce = new ReturnService();
        $ret = $returnSerivce->isReturnService(1972391302);
        dd($ret);
    }

    public function testReturnToDelivery()
    {
        $saleRow = \App\Facades\Sale\Sale::getSale(2041409199, [
            'id',
            'uri',
            'category',
            'secCategory',
            'type',
            'status',
            'isDel',
            'winUserinfoId',
            'dispute',
            'userinfoId',
            'profileJson',
            'uri',
            'launchTime'
        ]);
        $returnSerivce = new ReturnService();
        $returnSerivce->returnToDelivery($saleRow, 123, 341341, "tst", "");
    }

    public function testSystemReturn(){
        $saleRow = \App\Facades\Sale\Sale::getSale(1972391302, [
            'id',
            'uri',
            'category',
            'secCategory',
            'type',
            'status',
            'isDel',
            'winUserinfoId',
            'enableReturn',
            'winJson',
            'dispute',
            'userinfoId',
            'profileJson',
            'uri',
            'launchTime'
        ]);
        $returnSerivce = new ReturnService();
        $ret = $returnSerivce->systemAgreeReturn($saleRow);
        dd($ret);
    }

    public function testDashboardAgreeReturn(){
        $saleRow = \App\Facades\Sale\Sale::getSale(1972391302, [
            'id',
            'uri',
            'category',
            'secCategory',
            'type',
            'status',
            'isDel',
            'winUserinfoId',
            'enableReturn',
            'winJson',
            'dispute',
            'userinfoId',
            'profileJson',
            'uri',
            'launchTime'
        ]);
        $returnSerivce = new DashboardReturnService();
        $ret = $returnSerivce->dashboardAgreeReturn($saleRow, 22, "reqwre", "opName", 0);
        dd($ret);
    }

    public function testWorkerAgreeReturn(){
        $saleInfo = \App\Facades\Sale\Sale::getSale(1972391302, [
            'id',
            'uri',
            'category',
            'secCategory',
            'type',
            'status',
            'isDel',
            'winUserinfoId',
            'enableReturn',
            'winJson',
            'dispute',
            'userinfoId',
            'profileJson',
            'uri',
            'launchTime'
        ]);
        $createData = [
            'saleId' => $saleInfo->id,
            'role' => 'buyer',
            'userinfoId' => $saleInfo->userinfoId,
            'workOrderId' => 1,
            'workOrderDisputeResult' => 2,
            'returnRole' => 1,//1是库主，2是卖家
            'shopId' => $saleInfo->userinfoId,
            'refundMoney' => 111,
            'reason' => 11
        ];
        DashboardReturnService::getInstance()->workerAfterCommon($saleInfo, 1, 1, 1, "1");
        try {
            DashboardReturnService::getInstance()->workerAgreeReturn($saleInfo, $createData, 100, 0, 330001, 1, "1", "1", 111);
        } catch (ApiException $e) {
        }
    }

    public function testWorkerRejectReturn(){
        // $saleInfo = \App\Facades\Sale\Sale::getSale(1972391302, [
        //     'id',
        //     'uri',
        //     'category',
        //     'secCategory',
        //     'type',
        //     'status',
        //     'isDel',
        //     'winUserinfoId',
        //     'enableReturn',
        //     'winJson',
        //     'dispute',
        //     'userinfoId',
        //     'profileJson',
        //     'uri',
        //     'launchTime'
        // ]);
        // $returnSerivce = new DashboardReturnService();
        //
        // $ret = $returnSerivce->workerRejectReturn($saleInfo,100,"1");

        $isMoneyReturn = 1;
        $saleId = 1972391302;
        $action = 'close';
        $reasonId = 0;
        $reason = "test";
        $workOrderId = 1;
        $userinfoId = 2;   // 工单发起者ID
        $handleBy = '';
        $saleInfo = \App\Facades\Sale\Sale::getSale($saleId, ['uri', 'dispute', 'status', 'winUserinfoId', 'userinfoId', 'profileJson', 'winJson']);
        DashboardReturnService::getInstance()->workerAfterCommon($saleInfo, $workOrderId, 1, $userinfoId, $handleBy);
        $ret = DashboardReturnService::getInstance()->workerRejectReturn($saleInfo,$reasonId,$reason,$isMoneyReturn);
        dd($ret);
    }

    function testG(){
        // dd(ReturnService::getInstance()->getOrderReason("2007131516zpp0es"));
        dd(ReturnService::getInstance()->getOrderAfterRecordList(2041117722));
        dd($this->bus->order()->orderReturn->getOrderHandleRecordList(2041112429));
    }

    function testTd(){
        $sale = \App\Facades\Sale\Sale::getSaleExtend(34637,['afterSaleJson']);
        if (!empty($sale->afterSale)) {
            foreach ($sale->afterSale as $key => $_afterSale) {
                if (!empty(get_property($_afterSale, 'advanceRefund', ''))) {
                    $advanceRefund = $_afterSale->advanceRefund;
                    break;
                }
            }
        }
        var_dump($advanceRefund);
        $advanceRefund = OrderService::getInstance()->getSaleSectionRefund(34637);
        var_dump($advanceRefund);exit;
    }


}
