<?php


namespace Tests;

use App\ConstDir\BaseConst;
use App\Facades\Sale\Sale;
use App\Libraries\task\v2\Sale_Handle;
use App\Logic\Rate\BuyerRateLogic;
use App\ConstDir\ErrorConst;
use App\Services\Order\OrderService;
use App\Services\Rate\RateService;
use App\Utils\CommonUtil;
use PHPUnit\Framework\TestCase;
use WptBus\Bus;
use WptBus\Lib\Utils;
use WptOrder\OrderService\Facades\Order;


class OrderTest extends TestCase
{
    protected $config = [];

    protected $bus;

    public function setUp()
    {
        defined('TRACE_NAME') || define('TRACE_NAME', 'api');
        defined('TRACE_HOST') || define('TRACE_HOST', ip2long('127.0.0.1'));
        defined('TRACE_ID') || define('TRACE_ID', md5(TRACE_NAME . TRACE_HOST . uniqid() . rand(100000, 999999)));

        $this->config = [
            'order' => [ // 服务名
                'http' => [ // http配置
                    'servers' => [
//                        'http://10.3.7.2:8080',
                        'http://172.16.34.198:8080/',
                    ]
                ]
            ]
        ];

        $this->bus = new \WptBus\Bus($this->config);
        app()->instance('bus', $this->bus);
    }

    public function testGetSellerRateList()
    {

        $result = $this->bus->order()->sellerRate->getSellerRateList(45119, 1, 0, 0, 10, 0);
        var_dump($result);

    }

    public function testGetShopTags()
    {
        $result = $this->bus->order(true)->sellerRate->getShopTags(2, array("good", "system"));
        var_dump($result);
    }

    public function testGetShopRateTagList()
    {
        $result = $this->bus->order(true)->rate->getShopRateTagList(45119, 10001, 0, 10);
        var_dump($result);
    }

    function testGetSellerDetail()
    {
        $result = $this->bus->order(true)->sellerRate->getRateInfo(2, "1904301444lnzlq4");
        var_dump($result);
    }

    function testGetAppealDetail()
    {
        $result = $this->bus->order(true)->sellerRate->getAppealDetail(8612384, "19042313492d0lgy");
        var_dump($result);
    }

    function testSaveAppeal()
    {
        $result = $this->bus->order(true)->rate->saveRateAppeal(2, "1804081641o2jw8c", "tset", "sadfsafs", "[\"sdfasfd\"]");
        var_dump($result);
    }

    public function testAutoRate()
    {
        $result = $this->bus->order()->rate->autoRate(1972391560);
        $this->assertEquals("true", $result);
    }

    public function testClearTimeEndSaleRateTags()
    {
        $result = $this->bus->order()->workRate->clearTimeEndSaleRateTags();
        $this->assertEquals("true", $result);
    }

    public function testxx()
    {
        $br = new BuyerRateLogic();
        $br->userinfo = (object)['userinfoId' => "8615943"];
        $ret = $br->toRate("19120322221jp9w6", 4, "xxxxx", (object)[], 1, "[]");


//        $saleHandle = new Sale_Handle();
//        $saleHandle->push('RATELIST', [
//            'id' => 11,
//            'sid' => 11,
//            'stars' => -1 * 12,
//        ]);
    }


    public function testGetBuyerRateInfo()
    {
        $saleUri = "19080117029tv3vo";
        $sale = $this->bus->order()->buyerRate->getBuyerRateInfo("2", $saleUri);
        $this->assertEquals($saleUri, $result['saleInfo']['uri']);
    }

    public function testSetRateInvalid()
    {

        $ret = $this->bus->order()->buyerRate->setRateInvalid(array("userinfoId" => 2, "saleId" => 1807592711));
        var_dump($ret);
    }

    public function testSetRateAppealStatus()
    {
        $ret = $this->bus->order()->workRate->setRateAppealStatus(2, 176, 3);
        var_dump($ret);
    }

    public function testDeleteRate()
    {


        $ret = $this->bus->order()->buyerRate->deleteBuyerRate(array("userId" => 8612423, "uri" => "A1903256971vflqy"));


        var_dump($ret);
    }

    public function testAddRate()
    {
        $tags = array(array("id" => "8612348", "tagName" => "服务态度好", "isSelected" => true), array("id" => "8612347", "tagName" => "质量很好", "isSelected" => true));
        $imgs = "[\"202001074c0aomu1-jha6-ajla-y26o-ilattlmzeznn-W828H1104\"]";
        $params = [
            "userId" => 8620155,
            "uri" => "1904221431hpxk5j",
            "content" => "",
            "tags" => $tags ? json_encode($tags, JSON_UNESCAPED_UNICODE) : '[]',
            "imgs" => json_encode(array("imgs" => json_decode($imgs, true)), JSON_UNESCAPED_UNICODE),
            "stars" => 5,
            "isAnonymous" => 1,
        ];

        $ret = $this->bus->order()->buyerRate->addBuyerRate($params);
        var_dump($ret);
    }


    public function testGetRate()
    {

        $ret = $this->bus->order()->buyerRate->getBuyerRateInfo(8620155, "1907242003vyw7ec");

        var_dump($ret);

        if ($ret['code'] != 0) {
            Utils::throwException($ret['code'], $ret['msg']);
        }
        $sale = $ret['data'];
        if (!$sale) {
            Utils::throwException(500, "拍品不存在");
        }
        $sale["saleInfo"]["img"] = CommonUtil::combineImgUrl($sale["saleInfo"]["img"], 240);
        if ($sale["isHide"] == 1) {
            return ['isDel' => 1];
        }
        $sale["tags"] = json_decode($sale["tags"], true);

        $sale['imgList'] = $this->getRateImgList(json_decode($sale["imgList"], true));
        $sale['appendImgList'] = $this->getRateImgList(json_decode($sale["appendImgList"], true));
        var_dump($sale);
    }

    public function testContentReview(){
        $update["shield"] = 2;
        $update["reply"] = "***";
        $update["content"] = "***";
        $update["appendContent"] = "***";
        $info = $this->bus->order()->workRate->getWorkRateDetail(2,176);
        var_dump($info);
        if ($info["code"] == 0){
            $ret = $this->bus->order()->workRate->saveRate(2, $info["data"]["id"], $update);
        }

        dd($ret);
//        $resp = RateService::getInstance()->rateContentReview(2,1807592711,"test");
//        var_dump($resp);
    }

    function testBatchGetSaleRate(){
        $ids = array_map('intval', array("176","1807592711"));
        $info = $this->bus->order()->workRate->batchGetSaleRate(2,$ids);
        if($info["code"] == 0){
            $saleRateList = json_decode($info["data"],true);
        }
        var_dump($saleRateList);
    }

    public function getRateImgList($media, $size = 240, $num = 5)
    {
        $imgList = [];
        if ($imgs = get_property($media, 'imgs', [])) {
            foreach ($imgs as $key => $img) {
                if ($key >= $num) {
                    break;
                }
                $imgList[] = CommonUtil::combineImgUrl($img, $size);
            }
        }
        return $imgList;
    }

    public function testGetOrderByUri()
    {
      // dd(Sale::getSaleExtend("2003161449na9ge2", null));


        $ret = $this->bus->order()->order->getOrderByUriOrId("2003161449na9ge2", ["saleId", "winUserinfoId"],['uri','profileJson']);

        var_dump($ret);

        $data = $ret['data'];
        var_dump($data['winUserinfoId']);

    }

    public function testGetOrderList()
    {
        $orderFields = ['orderId', 'winJson','modifyTime'];
        $saleFields = [
            'uri',
            'status',
            'createTime',
            'profileJson',
            'priceJson',
            'enableReturn'
        ];
        $ret = $this->bus->order()->order->getBuyerOrderList(2,[
            'status' => [
        1,2,3
            ]
        ], $orderFields,$saleFields);

        var_dump($ret);

        $data = $ret['data'];
        var_dump($data['winUserinfoId']);
    }
}