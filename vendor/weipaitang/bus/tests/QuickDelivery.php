<?php

use App\ConstDir\ErrorConst;
use App\ConstDir\OrderConst;
use App\ConstDir\ResidueConst;
use App\ConstDir\SaleConst;
use App\Facades\Sale\SaleExtend;
use App\Logic\ExpressLogic;
use App\Logic\OrderLogic;
use App\Models\OrderModel;
use App\Models\OrderPrepaymentModel;
use App\Services\Order\SpringFestivalService;
use App\Services\Residue\ResidueService;
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
        $ip = $ip['en0'] ?? '';
        $ip = "172.16.34.198";
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

    public function test_GetListByDate()
    {
        $endDate = date('Ymd', strtotime("-60days"));
        echo $endDate;
        $rest = $this->bus->order()->quickDelivery->getQuickDeliveryTotalByDate(2, (int)$endDate);
        if ($rest["code"] > 0) {

        }
        $list = $rest["data"];
        $newList = [];
        foreach ((array)$list as $key => $val) {
            $newList[$key]["code"] = $val["date"];
            $newList[$key]["deliveryNum"] = $val["total"];
            $newList[$key]["date"] = strtotime($val["date"]);
        }
        var_dump($newList);
    }
    public function test_exportList()
    {
        $rest = Bus::order()->quickDelivery->exportQuickDeliveryList(2, [20180830],1);
        var_dump($rest);
    }

    public function test_GetList()
    {
        $rest = Bus::order()->quickDelivery->getQuickDeliveryList(2, [20180830]);
        if ($rest["code"] != 0) {
            CommonUtil::throwException(ErrorConst::ERROR_CODE, $rest["msg"]);
        }
        $list = $rest["data"];
        $saleInfo = $this->newBuildSellerList($list);
        foreach ($list as $key => &$item) {
            if (isset($saleInfo[$item->saleId])) {
                $item->sale = $saleInfo[$item->saleId]['sale'];
            } else {
                $list[$key] = null;
            }
        }
        unset($item);
        $newList = array_values(array_filter($list));
        dd($newList);
    }

    private function newBuildSellerList($saleList)
    {
        $outDataList = [];
        if ($saleList) {
            foreach ($saleList as $key => $item) {
                $sale = $item->sale;
                $sale->orderId = $item->saleId;
                //是否产品库主
                $isPdSelf = false;
                if ($sale->depotUserId > 0) {
                    $isPdSelf = ($sale->userinfoId == $sale->depotUserId);
                }
                //合买发货期限15天，其他3天
                $delayTime = $sale->type == SaleConst::SALE_TYPE['liveGroupBuy'] ?
                    OrderConst::GROUP_DELIVERY_DEADLINE_DAY : OrderConst::DELIVERY_DEADLINE_DAY;
                $deliveryEndTime = get_property($sale, 'paidTime', 0) + $delayTime;
                SpringFestivalService::getInstance()->checkDeliveryDeadline($deliveryEndTime, $sale->type, $sale->orderId);

                $deliveryEndTime += ($sale->delayStatus == 7) ? ($sale->delayDay * 86400) : 0;
                $outData['sale'] = [
                    'standardGoodsUri' => $sale->standardGoodsUri ?? '',
                    'img' => ImageUtil::combineImgUrl($sale->cover, 240),
                    'content' => Str::substr(strip_tags(html_entity_decode($item->title)), 20, true),
                    'enableReturn' => $sale->enableReturn > 0,                                       //是否包退1是，2确认收货后已申请7天包退
                    'isFreePost' => $sale->expressFee == 'freePost',                                 //是否包邮
                    'bidBzj' => (int)$sale->bidbzj,                                           // 保证金
                    'remarkContent' => $sale->maskContent,                                           // 备注信息
                    'winPrice' => $sale->price ?? null,                                         //成交价
                    'buyerNickname' => $sale->buyerNickname ?? '',
                    'deliveryEndTime' => $deliveryEndTime,                                    //发货期限
                    'endTime' => (int)$sale->endTime ?: null,                                 //截止时间
                    'pdPrice' => $sale->depotPdPrice,             //产品库供货价
                    'isPdSale' => $sale->depotUserId > 0 ? 1 : 0, //产品库拍品
                    'isCloudPd' => $sale->depotId == 1 ? 1 : 0,   //云库拍品
                    'isPdSelf' => $isPdSelf,
                ];
                $outDataList[$sale->orderId] = $outData;
            }
        }
        return $outDataList;
    }
}
