<?php


namespace Tests;

use App\ConstDir\ErrorConst;
use App\Facades\Sale\SaleExtend;
use App\Logic\ExpressLogic;
use App\Logic\OrderLogic;
use App\Models\OrderModel;
use App\Models\OrderPrepaymentModel;
use App\Services\Order\OrderService;
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

class DeliveryTest extends TestCase
{
    protected $config = [];

    protected $bus;

    public function setUp()
    {
        defined('TRACE_NAME') || define('TRACE_NAME', 'api');
        defined('TRACE_HOST') || define('TRACE_HOST', ip2long('127.0.0.1'));
        defined('TRACE_ID') || define('TRACE_ID', md5(TRACE_NAME . TRACE_HOST . uniqid() . rand(100000, 999999)));
        // $ip = swoole_get_local_ip();
        $ip = "172.16.35.169";

        $ip = $ip['en0'] ?? '';
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

    static $orderDB;

    /**
     * @return Connection
     */
    private function getOrderDB()
    {
        if (self::$orderDB instanceof Connection) {
            return self::$orderDB;
        }
        $config = [
            'database.connections.wpt_order' => [
                'host' => env('WPT_SALE_RATE_MASTER_DB_HOST'),
                'port' => env('WPT_SALE_RATE_MASTER_DB_PORT'),
                'database' => 'wpt_order',
                'username' => env('WPT_SALE_RATE_MASTER_DB_USERNAME'),
                'password' => env('WPT_SALE_RATE_MASTER_DB_PASSWORD'),
                'driver' => 'mysql',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'prefix' => '',
            ]
        ];
        config($config);
        self::$orderDB = \DB::connection('wpt_order');
        return self::$orderDB;
    }

    public function test_dumps()
    {
        // $saleIds = [1972388896, 2041149021, 2041149010, 2041149006, 2041149018, 1972391183, 1972391133];
        $saleIds = [1904313447];
        foreach ($saleIds as $id) {
            $this->dump($id);
        }
    }

    private function dump($id)
    {
        $this->dumpDeliverJson($id);
        $this->dumpLogistics($id);
        $this->dumpReturnAddressAndLogistics($id);
        $this->dumpDelayJson($id);
    }


    private function dumpDelayJson($saleId)
    {
        $uri = Sale::getSale($saleId, ['uri'])->uri ?? '';
        $sale = (new OrderLogic())->getSale($uri);
        $winJson = json_decode($sale->winJson, true);
        $delay = array_only($winJson, ['deliveryDelayDealStatus', 'deliveryDelayApplyAt', 'deliveryDelayDay', 'deliveryDelayReason']);
        if (empty($delay)) {
            return;
        }
        $this->getOrderDB()->table('order_delay_delivery')->updateOrInsert([
            'orderId' => $saleId
        ], [
            'deliveryDelayDealStatus' => $delay['deliveryDelayDealStatus'] ?? 0,
            'deliveryDelayApplyAt' => $delay['deliveryDelayApplyAt'] ?? null,
            'deliveryDelayDealAt' => $delay['deliveryDelayDealAt'] ?? null,
            'deliveryDelayDay' => $delay['deliveryDelayDay'] ?? '',
            'deliveryDelayReason' => $delay['deliveryDelayReason'] ?? '',
        ]);
    }

    private function dumpDeliverJson($saleId)
    {
        $order = OrderModel::getInstance()->getOne(['deliverJson'], ['saleId' => $saleId, 'status' => 'paid', 'type' => 'residue']);
        if (empty($order->deliverJson)) {
            return;
        }
        if ($order) {
            $order->deliver = json_decode(filterJSON($order->deliverJson, true));
        }
        $deleivry = $order->deliver;
        $this->getOrderDB()->table('order_address')->updateOrInsert([
            'orderId' => $saleId,
            'addressType' => 0,
        ], [
            'proviceFirstStageName' => $deleivry->proviceFirstStageName ?? '',
            'addressCitySecondStageName' => $deleivry->addressCitySecondStageName ?? '',
            'addressCountiesThirdStageName' => $deleivry->addressCountiesThirdStageName ?? '',
            'addressDetailInfo' => $deleivry->addressDetailInfo ?? '',
            'addressPostalCode' => $deleivry->addressPostalCode ?? '',
            'nationalCode' => $deleivry->nationalCode ?? '',
            'telNumber' => $deleivry->telNumber ?? '',
            'userName' => $deleivry->userName ?? '',
            'createTime' => time(),
        ]);
    }

    private function dumpLogistics($saleId)
    {
        $extend = SaleExtend::getSaleExtend($saleId, ['saleId', 'deliveryJson']);
        if (empty($extend->delivery)) {
            return;
        }

        $this->getOrderDB()->table('order_logistics')->updateOrInsert([
            'orderId' => $saleId,
            'logisticsType' => 0
        ], [
            'com' => $extend->delivery->com,
            'code' => $extend->delivery->code,
            'imgId' => $extend->delivery->imgId ?? '',
            'comSnap' => $extend->delivery->comSnap ?? '',
            'codeSnap' => $extend->delivery->codeSnap ?? '',
            'imgIdSnap' => $extend->delivery->imgIdSnap ?? '',
            'hasModified' => $extend->delivery->hasModified ?? 0,
            'expressModifyTime' => $extend->delivery->expressModifyTime ?? 0,
            'expressSubscribeId' => $extend->delivery->expressSubscribeId ?? '',
            'createTime' => time(),
        ]);
    }

    private function dumpReturnAddressAndLogistics($saleId)
    {
        $extend = SaleExtend::getSaleExtend($saleId, ['saleId', 'returnDeliveryJson']);
        if (!empty($extend->returnAddress)) {
            $deleivry = $extend->returnAddress;
            $this->getOrderDB()->table('order_address')->updateOrInsert([
                'orderId' => $saleId,
                'addressType' => 1,
            ], [
                'proviceFirstStageName' => $deleivry->proviceFirstStageName ?? '',
                'addressCitySecondStageName' => $deleivry->addressCitySecondStageName ?? '',
                'addressCountiesThirdStageName' => $deleivry->addressCountiesThirdStageName ?? '',
                'addressDetailInfo' => $deleivry->addressDetailInfo ?? '',
                'addressPostalCode' => $deleivry->addressPostalCode ?? '',
                'nationalCode' => $deleivry->nationalCode ?? '',
                'telNumber' => $deleivry->telNumber ?? '',
                'userName' => $deleivry->userName ?? '',
                'createTime' => time(),
            ]);
        }

        if (!empty($extend->returnDelivery)) {
            $this->getOrderDB()->table('order_logistics')->updateOrInsert([
                'orderId' => $saleId,
                'logisticsType' => 1
            ], [
                'com' => $extend->returnDelivery->com,
                'code' => $extend->returnDelivery->code,
                'imgId' => $extend->returnDelivery->imgId ?? '',
                'comSnap' => $extend->returnDelivery->comSnap ?? '',
                'codeSnap' => $extend->returnDelivery->codeSnap ?? '',
                'imgIdSnap' => $extend->returnDelivery->imgIdSnap ?? '',
                'hasModified' => $extend->returnDelivery->hasModified ?? 0,
                'expressModifyTime' => $extend->returnDelivery->expressModifyTime ?? 0,
                'expressSubscribeId' => $extend->returnDelivery->expressSubscribeId ?? '',
                'createTime' => time(),
            ]);
        }
    }

    public function test_agreeRestore()
    {
        $saleId = 2041149021;
        $orderModel = new OrderModel();
        $order = $orderModel->setWriteConnect()->getDeliverEntries(['deliverJson'], $saleId);
        // 订单二期-发货数据层重构
        $eq = DataCompare::getInstance()->skip(['addressId', 'err_msg'])->handle(
            'gray_order_delivery',
            $order->deliver,
            function () use ($saleId) {
                $ret = Bus::order()->delivery->getDeliveryAddress($saleId);
                if ($ret['code'] != 0) {
                    Utils::throwException(100, '发货地址不存在');
                }
                $deliver = $ret["data"];
                return (object)$deliver;
            }
        );
        $this->assertTrue($eq);
    }

    public function test_xx()
    {
        dd(typeOf());
        // $s = (new ExpressLogic())->sale('1911211935f3sb7o', 1, 1);
        $s = (new ExpressLogic())->sale('1911211935f3sb7o', 0, 1);
    }


    public function test_orderModelGetOne()
    {
        $order = Order::getOrderById(2041149021, ['saleId', 'winUserinfoId']);
        $whereSql = [
            'saleId' => $order->saleId,
            'userinfoId' => $order->winUserinfoId,
            'type' => 'residue'
        ];
        $order = (new OrderModel())->getOne(['deliverJson', 'status'], $whereSql);
        $this->assertTrue(true);
    }

    public function test_orderModelGetList()
    {
        $saleIds = [2041149021, 2041149010];

        $where = [
            'type' => 'residue',
            'saleId' => $saleIds,
            'status' => 'paid',
        ];

        $orderList = (new OrderModel())->getList(['id', 'saleId', 'userinfoId', 'deliverJson'], $where);
        if (empty($orderList)) {
            return [];
        }

        $ret = DataCompare::getInstance()->handle('gray_order_delivery', true, function () use ($orderList, $where) {
            $orderList = collect($orderList);
            $saleIds = $orderList->pluck('saleId')->toArray();
            $ret = Bus::order()->delivery->batchGetDeliveryAddress($saleIds);
            if ($ret['code'] != 0) {
                Utils::throwException(100, '获取发货地址失败');
            }
            $addrs = $ret["data"];
            $addrs = collect($addrs)->keyBy('orderId');
            $eq = true;
            $orderList->each(function ($item) use ($addrs, &$eq) {
                $eq &= (empty($addrs->get($item->saleId)) === empty($item->deliverJson));
            });
            return (bool)$eq;
        });
        $this->assertTrue($ret);
    }

    public function test_extend_list()
    {
        $saleIds = [1972388896, 2041149021, 1972391183, 1972391133, 1972390554];
        $fields = ['saleId', 'returnDeliveryJson', 'deliveryJson'];
        $list = SaleExtend::batchGetSaleExtendSaleGo($saleIds, $fields);
        // 订单二期-发货数据层重构
        $eq = DataCompare::getInstance()->skip(['.returnAddress.err_msg'])
            ->handle('gray_order_delivery', $list, function () use ($saleIds, $fields) {
                return SaleExtend::batchGetSaleExtendDirectly($saleIds, $fields);
            });
        $this->assertTrue($eq);
    }

    public function test_sale_get_extend()
    {
        $saleId = 1972391183;
        $saleIdUri = Sale::getSale($saleId, ['uri'])->uri ?? '';

        $fields = ['saleId', 'returnDeliveryJson', 'deliveryJson'];

        $extend = \App\Facades\Sale\Sale::getSaleExtend($saleIdUri, $fields);
        $this->assertNotEmpty($extend);
    }

    public function test_se_get_extend()
    {
        $saleId = 1972391183;
        $saleIdUri = Sale::getSale($saleId, ['uri'])->uri ?? '';

        $fields = ['saleId', 'returnDeliveryJson', 'deliveryJson'];
        $extend = SaleExtend::getSaleExtendSaleGo($saleIdUri, $fields);

        // 订单二期-发货数据层重构
        $eq = DataCompare::getInstance()->skip(['returnAddress.err_msg'])->handle(
            'gray_order_delivery',
            $extend,
            function () use ($saleId, $fields) {
                $extend = SaleExtend::getSaleExtendDirectly($saleId, $fields);
                return $extend;
            }
        );
        $this->assertTrue($eq);
    }

    public function test_appendSaleExtend()
    {
        $condition = [
            'status' => OrderStatus::RETURNING,
            'launchTime <' => time() - 2 * 86400,
            'dispute <=' => 1
        ];
        $orderFields = [
            'saleId',
            'snapshot',
            'saleType',
            'isRated',
            'paidTime',
            'finishedTime',
            'delayPayTime',
            'deliveryTime',
            'endTime',
            'status',
            'userinfoId',
            'winUserinfoId',
            'winJson',
            'dispute'
        ];
        $saleFields = [
            'uri',
            'category',
            'secCategory',
            'recommendTime',
            'createTime',
            'multiWins',
            'profileJson',
            'priceJson',
            'enableReturn'
        ];
        $saleList = Order::getOrderListAttachSale($condition, $orderFields, $saleFields, null, null, 'launchTime asc');
        foreach ($saleList as $sale) {
            $this->dump($sale->saleId);
            $sale = \App\Facades\Sale\Sale::appendSaleExtend($sale);
            $this->assertNotEmpty($sale);
        }
    }


    public function test_getOrderDelayDeliveryInfo()
    {
        $saleId = 1904313447;
        $uri = Sale::getSale($saleId, ['uri'])->uri ?? '';
        $sale = (new OrderLogic())->getSale($uri);
        $winJson = json_decode($sale->winJson, true);
        DataCompare::getInstance()->handle('gray_order_delivery', function () use ($winJson) {
            return array_only($winJson, ['deliveryDelayDealStatus', 'deliveryDelayApplyAt', 'deliveryDelayDay', 'deliveryDelayReason']);
        }, function () use ($sale) {
            $ret = Bus::order()->delivery->getOrderDelayDeliveryInfo($sale->id);
            if ($ret['code'] != 0) {
                Utils::throwException(100, '获取延迟发货信息失败');
            }
            $delayInfo = $ret["data"];
            return $delayInfo;
        });
    }


    public function test_getDeliveryDelayDay()
    {
        $saleId = 1904313447;
        $uri = Sale::getSale($saleId, ['uri'])->uri ?? '';
        $sale = (new OrderLogic())->getSale($uri);
        $winJson = json_decode($sale->winJson, true);
        $ret = Bus::order()->delivery->getDeliveryDelayDay($sale->id ?? 0, $winJson);
        $this->assertNotEmpty($ret);
    }

    public function test_isDeliveryDelayApplied()
    {
        $saleId = 1904313447;
        $uri = Sale::getSale($saleId, ['uri'])->uri ?? '';
        $sale = (new OrderLogic())->getSale($uri);
        $winJson = json_decode($sale->winJson, true);
        $ret = Bus::order()->delivery->isDeliveryDelayApplied($sale->id, $winJson);
        $this->assertNotEmpty($ret);
    }

    public function test_getDeliveryDelayInfo()
    {
        $saleId = 1904313447;
        $uri = Sale::getSale($saleId, ['uri'])->uri ?? '';
        $sale = (new OrderLogic())->getSale($uri);
        $winJson = json_decode($sale->winJson, true);
        $ret = Bus::order()->delivery->getDeliveryDelayInfo($sale->id, $winJson);
        $this->assertNotEmpty($ret);
    }

    public function test_SaleCorpAddress()
    {
        $orderModel = new OrderModel();
        $orderInfo = $orderModel->getOne($orderModel->intFilterColumns, ['id' => 64880]);
        $addressList = UserAddress::getInstance()->getList($orderInfo->userinfoId);
        $rs = DataCompare::getInstance()->ab(
            'order_delivery_ab',
            function () use ($orderModel, $addressList, $orderInfo) {
                return $orderModel->updateData(
                    ['deliverJson' => json_encode($addressList[$addressList['default']])],
                    ['id' => $orderInfo->id]
                );
            },
            function () use ($orderInfo, $addressList) {
                $ret = Bus::order()->delivery->addOrderDeliveryAddress(
                    $orderInfo->saleId,
                    $addressList[$addressList['default']]
                );
                return $ret['code'] == 0 and $ret['data'];
            }
        );
        $this->assertEquals(true, (bool)$rs);
    }

    public function test_general_order_insert()
    {
        $saleId = 1904321773;
        $orderPrepaymentModel = new OrderPrepaymentModel();
        $columns = [
            'id',
            'businessOrderNo',
            'targetId',
            'targetUri',
            'payTime',
            'payMethod',
            'orderNo',
            'outTradeNo',
            'cardType',
            'userinfoId',
            'orderMoney',
            'orderStatus',
            'payPostData',
            'payBackInfo',
            'deliveryJson',
            'bankCardJson'
        ];
        $orderPrepaymentRow = $orderPrepaymentModel->setWriteConnect()->getOne(
            $columns,
            ['targetId' => $saleId, 'orderStatus' => 1]
        );

        $deliveryJson = get_property($orderPrepaymentRow, 'deliveryJson', '');

        //增加订单
        $orderData = [
            "body" => "买家货款",
            "saleId" => $orderPrepaymentRow->targetId,
            "userinfoId" => $orderPrepaymentRow->userinfoId,
            "out_trade_no" => $orderPrepaymentRow->outTradeNo,
            "total_fee" => $orderPrepaymentRow->orderMoney,
            "status" => "paid",
            "type" => "residue",
            "deliverJson" => Str::filterJSON($deliveryJson),
            "payMethod" => $orderPrepaymentRow->payMethod,
            "cardType" => $orderPrepaymentRow->cardType,
            'isNewPay' => true //是否走的新支付接口
        ];
        $orderId = (new OrderModel())->insertEntry($orderData['userinfoId'], $orderData);
        $this->assertNotEmpty($orderId);
    }

    public function test_general_order_update()
    {
        // 1972388896, 2041149021, 2041149010, 2041149006, 2041149018, 1972391183, 1972391133
        $saleId = 1972388896;
        $order = OrderModel::getInstance()->getOne(['id', 'saleId', 'userinfoId', 'deliverJson'], ['saleId' => $saleId]);
        // dd($order);
        $deliverJson = $order->deliverJson ?? '';

        // $deliverJson = '{"addressCitySecondStageName":"北京市","addressCountiesThirdStageName":"东城区","addressDetailInfo":"1231231212","proviceFirstStageName":"北京市","telNumber":"13067879793","userName":"huangxxx","addressId":"1630001","addressPostalCode":"","err_msg":"alCode":""}';
        $update = [
            'deliverJson' => '',
            'updateTime' => time(),
            'total_fee' => 123
        ];
        $ret = (new OrderModel())->updateData($update, ['id' => get_property($order, 'id')]);
        $this->assertEquals(1, $ret);
    }


    public function test_order_get_one()
    {
        $filed = array('out_trade_no', 'deliverJson', 'total_fee', 'createTime');
        $where = array(
            'saleId' => '2041009289',
            'userinfoId' => '2',
            'status' => array('paid', 'refunding', 'refunded', 'finished'),
            // 'deliverJson !=' => '',
            'type' => 'residue'
        );
        $one = OrderModel::getInstance()->getOne($filed, $where);
        dd($one);
    }

    public function test_order_insert_delivery_null()
    {
        $userinfo_address = UserAddress::getInstance()->getByType(1111111111111, UserAddress::DELIVERY_ADDR);
        $orderData = [
            "body" => "买家货款",
            "saleId" => 1212121,
            "userinfoId" => 1212,
            "out_trade_no" => '121212',
            "total_fee" => 1.22,
            "status" => "paid",
            "type" => "residue",
            "deliverJson" => filterJSON(json_encode($userinfo_address, JSON_UNESCAPED_UNICODE)),
            "payMethod" => 'balance',
            "cardType" => 'balance'
        ];
        $orderId = OrderModel::getInstance()->insertEntry(1212, $orderData);
        dd($orderId);
    }

    public function test_batchDeliveryDelayDays()
    {
        $remarkSaleIds = [48, 49];
        $saleList = \App\Facades\Sale\Sale::getSale($remarkSaleIds, ['id', 'type',], ['isDel' => 0]);
        $saleIds = array_pluck($saleList, 'id');
        $ret = Bus::order()->delivery->batchDeliveryDelayDays($saleIds);
        $this->assertEquals([48 => 1, 49 => 1], $ret);
    }

    // 发货信息【拍品】
    public function test_getOrderAuctionDeliveryInfo()
    {

        $saleUri = "20030317491qbvy1";
        $userinfoId = 2;
        $addressIndex = 0;
        $data = Bus::order()->delivery->getOrderAuctionDeliveryInfo($saleUri, $userinfoId, $addressIndex);

        // dd($data);

        if ($data["code"] != 0) {
            CommonUtil::throwException(ErrorConst::ERROR_CODE, $data["msg"]);
        }

        // dd($data["data"]);

        $saleList = $data["data"]["togetherList"] ?? [];
        $saleInfo = $data["data"]["saleInfo"] ?? [];
        $returnAddress = $data["data"]["returnAddr"] ?? [];
        $deliverAddr = $data["data"]["deliverAddr"] ?? [];

        // 排序处理
        $salesTogether = [];
        // 订单二期-发货数据层重构
        if (count($saleList) > 0) {
            foreach ($saleList as $item) {
                $salesTogether[] = [
                    'uri' => $item["uri"],
                    'type' => $item["type"],
                    'expressFee' => $item["expressFee"],
                    'content' =>  $item["title"],
                    'paidTime' => date('m月d日 H:i', $item["paidTime"]),
                    'status' => $item["status"],
                    'createTime' => $item["createTime"],
                    'img' => ImageUtil::combineImgUrl($item["cover"], 240),
                ];
            }
        }
        $createTimes = array_column($salesTogether, 'createTime');
        array_multisort($createTimes, SORT_DESC, $salesTogether);

        $result =  [
            'salesDeliveryTogether' => $salesTogether?: [],
            'sale' => [
                'uri' => $saleUri,
                'type' => $saleInfo["type"],
                'expressFee' => $saleInfo["expressFee"] ,
                'content' => $saleInfo["title"],
                'paidTime' => date('m月d日 H:i', $saleInfo["paidTime"]),
                'status' =>$saleInfo["status"] ,
                'img' => ImageUtil::combineImgUrl($saleInfo["cover"], 240),
            ],
            'deliver' => empty($deliverAddr) ? [] : [
                'proviceFirstStageName' => $deliverAddr['proviceFirstStageName'],
                'addressCitySecondStageName' => $deliverAddr['addressCitySecondStageName'],
                'addressCountiesThirdStageName' => $deliverAddr['addressCountiesThirdStageName'],
                'addressDetailInfo' => $deliverAddr['addressDetailInfo'],
                'telNumber' => str_replace("-", "", $deliverAddr['telNumber']),
                'userName' => $deliverAddr['userName'],
            ],
            'addressIndex' => $addressIndex,
            'hasAddressList' => !empty($returnAddress) ? 1 : 0,
            'returnAddress' => $returnAddress,
            // 'hotDeliveryList' => $hotDeliveryList,
            // 'normalDeliveryList' => $normalDeliveryList,
            'isPdSale' => $saleInfo["depotId"] > 0 ? 1 : 0,      //是否产品库拍品，由卖家确认退款
            'isHighScalpingRate' => app('UserService')->isHighScalpingRate($saleInfo["winUserinfoId"]),//是否高刷单率买家
        ];

        var_dump(json_encode($result, true));
    }

    // 发货信息【产品库】
    public function test_getOrderDepotDeliveryInfo()
    {
        $saleUri = "1808301724jvpm7t";
        $userinfoId = 2;
        $addressIndex = 0;
        $data = Bus::order()->delivery->getOrderDepotDeliveryInfo($saleUri, $userinfoId, $addressIndex);

        // dd($data);
        if ($data["code"] != 0) {
            CommonUtil::throwException(ErrorConst::ERROR_CODE, $data["msg"]);
        }

        dd(json_encode($data["data"]));

        $saleInfo = $data["data"]["saleInfo"];
        $returnAddress = $data["data"]["returnAddr"];
        $deliverAddr = $data["data"]["deliverAddr"];

        // dd(get_property($saleInfo["profile"], 'imgs'));

        $result = [
            'sale' => [
                'uri' => $saleUri,
                'type' => $saleInfo["type"],
                'expressFee' => $saleInfo["expressFee"],
                'content' => $saleInfo["title"],
                'paidTime' => date('m月d日 H:i', $saleInfo["paidTime"]),
                'agentNickname' => $data["data"]['nickname'] ?? '',
                'img' => ImageUtil::combineImgUrl($saleInfo["cover"], 240),
                'depotId' => $saleInfo["depotId"],
                'productId' => $saleInfo["depotPdId"],
                'winUserinfoId' => $orderInfo["winUserinfoId"],
            ],
            'deliver' => empty($deliverAddr) ? [] : [
                'proviceFirstStageName' => $deliverAddr['proviceFirstStageName'],
                'addressCitySecondStageName' => $deliverAddr['addressCitySecondStageName'],
                'addressCountiesThirdStageName' => $deliverAddr['addressCountiesThirdStageName'],
                'addressDetailInfo' => $deliverAddr['addressDetailInfo'],
                'telNumber' => str_replace("-", "", $deliverAddr['telNumber']),
                'userName' => $deliverAddr['userName'],
            ],
            'addressIndex' => $addressIndex,
            'hasAddressList' => !empty($returnAddress) ? 1 : 0,
            'returnAddress' => $returnAddress,
            // 'hotDeliveryList' => $hotDeliveryList,
            // 'normalDeliveryList' => $normalDeliveryList,
            'isHighScalpingRate' => app('UserService')->isHighScalpingRate($saleInfo["winUserinfoId"]),//是否高刷单率买家
        ];

        var_dump(json_encode($result));
    }


    // 绑定地址到订单上
    public function test_bindOrderAddress()
    {
        $saleUri = "2003061542um6s4z";
        $userinfoId = 8622277;
        $addressIndex = 41671;
        $data = Bus::order()->delivery->bindOrderAddress($saleUri, $userinfoId, $addressIndex);
        if ($data["code"] != 0) {
            CommonUtil::throwException(ErrorConst::ERROR_CODE, $data["msg"]);
        }
        if (!isset($data["data"]["saleOrder"]) || !isset($data["data"]["addr"])) {
            CommonUtil::throwException(ErrorConst::ERROR_CODE, "我疯了");
        }
        dd(json_encode($data));
    }

    // 卖家主动提醒买家绑定地址
    public function test_remindBuyerBindAddress()
    {
        $saleUri = "1804200138ug4xxh";
        $userinfoId = 1;
        $data = Bus::order()->delivery->remindBuyerBindAddress($saleUri, $userinfoId);
        dd($data);
        if ($data["code"] != 0) {
            CommonUtil::throwException(ErrorConst::$data["code"], $data["msg"]);
        }
    }

    // 提醒绑定地址按钮
    public function test_getRemindBindAdd()
    {
        $saleId = 11713;
        $userinfoId = 6;
        $data = Bus::order()->delivery->getRemindBindAdd($saleId, $userinfoId);
        dd($data);
        if ($data["code"] != 0) {
            CommonUtil::throwException(ErrorConst::$data["code"], $data["msg"]);
        }
    }
}
