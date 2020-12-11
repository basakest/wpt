<?php


namespace Tests;


use PHPUnit\Framework\TestCase;
use WptBus\Lib\Error;

class FrameworkTest extends TestCase
{
    protected $config = [];

    protected $bus;

    public function setUp()
    {
        defined('TRACE_NAME') || define('TRACE_NAME', 'api');
        defined('TRACE_HOST') || define('TRACE_HOST', ip2long('127.0.0.1'));
        defined('TRACE_ID') || define('TRACE_ID', md5(TRACE_NAME . TRACE_HOST . uniqid() . rand(100000, 999999)));
        $ip = "172.16.33.176";
        $ip = "10.3.7.34";
        $ip = "10.3.7.51";
        $ip = "127.0.0.1";
        $this->config = [
            'user' => [ // 服务名
                'http' => [ // http配置
                    'servers' => [
                        "http://$ip:8080/",
                    ]
                ]
            ],
            'shop' => [ // 服务名
                'http' => [ // http配置
                    'servers' => [
                     "http://$ip:8080/",
                    ]
                ]
            ]
        ];

        $this->bus = new \WptBus\Bus($this->config);
    }


    public function test_errorGetBusErrorMsgInfo()
    {
        $msg = '{"name":"user","url":"http:\/\/test-micro-gw.wptqc.com\/user\/info\/get-base-info","body":{"identity":"8611679","field":["userinfoId","userType"]},"opts":{"headers":{"traceId":"f6f42e352153fa872012f8dc9aa73357","unique_id":"f6f42e352153fa872012f8dc9aa73357","client-cookie":"wptTouristUri=M1912241645x20kh; wptCurrentUserLevel=%E8%87%AA%E5%AE%9A%E4%B9%89; wptCurrentUser=2; wptSessionId=20200803102500_tlgksum3sq; wpt_mock_cookie=11dee6a9b9a62c4c6e755041fccdf69d47876287c173b0273b14d73bf174df52e82ce4ebc3b0b9293608635e43e6ffd0dd92ef65698d1cd53c3f379d465b3365; wpt_mock_user_type=undefined; h5V=defaultVerison; wpt_env_num=tke_05; identity=2541add08fc8fce690f80636354e5d91","client-user-agent":"Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/84.0.4147.105 Safari\/537.36","client-remote-addr":"10.3.7.161","client-x-forwarded-for":"10.3.7.161","client-referer":"","client-platform":"","client-request-uri":"\/debug\/user\/8611679"}},"result":"<html>\r\n<head><title>503 Service Temporarily Unavailable<\/title><\/head>\r\n<body>\r\n<center><h1>503 Service Temporarily Unavailable<\/h1><\/center>\r\n<hr><center>nginx\/1.15.10<\/center>\r\n<\/body>\r\n<\/html>\r\n","duration":0.681,"curlErrno":0,"curlError":"","httpCode":503,"currentRetry":0}';
        echo Error::getBusErrorMsgInfo(Error::TRANSPORT, $msg);
        echo "\n";
        $data = null;
        echo Error::getBusErrorMsgInfo(Error::RETURN_FORMAT_ERROR, json_encode($data));
        echo "\n";
        $errorLogInfo = ['uri' => "127.0.0.1", 'params' => [], 'data' => [], 'unique_id' => "dddddd"];
        echo Error::getBusErrorMsgInfo(Error::SYSTEM_EXCEPTION, json_encode($errorLogInfo));
        echo "\n";
    }

    public function test_httpPostError()
    {
//        $result = $this->bus->user()->user->getBaseInfo("8611679", [123, 456]);
//        var_dump($result);

//        $config = [
//            'servers' => [
//                "http://127.0.0.1:8080/",
//            ]
//        ];
//        $result = $this->bus->user()->user->setHttpConfig($config)->getBaseInfo("8611679", ["id", "uri"]);
//        var_dump($result);

        $config = [
            'servers' => [
//                "http://localhost:8080/",
                "http://10.3.7.34:8080/",
            ]
        ];
        $result = $this->bus->user()->user->setHttpConfig($config)->getBaseInfo("8611679", ["id", "uri"]);
        var_dump($result);
    }

    public function test_getUidByUnionId()
    {
        $result = $this->bus->user()->search->getUidByUnionId("o_Jgat74BJYRDhIFqFTVzDed1nN4dd");
        var_dump($result);
    }

    public function test_getBlackInfo()
    {
        $result = $this->bus->user()->userRelation->getBlackInfo(8611679);
        echo json_encode($result);
        print_r($result);
    }

    public function testSearchByCompanyName()
    {
        $ret = $this->bus->user()->search->searchByCompanyName("北京元懋翔万达丰科文化发展有限公司");
        echo json_encode($ret);
    }

    public function testSearchByShopName()
    {
        $ret = $this->bus->user()->search->searchByShopName("夕");
        echo json_encode($ret);
    }

    public function testSearchByNameWithProperty()
    {
        $ret = $this->bus->user()->search->searchByNameWithProperty("打天下坐江山",[1001]);
        echo json_encode($ret);
    }

    public function testSearchByName()
    {
        $ret = $this->bus->user()->search->searchByName("cLy");
        echo json_encode($ret);
    }

    public function testGetUidByDeviceId()
    {
        $ret = $this->bus->user()->deviceId->getUidByDeviceId("00065d491efe6b8e");
        echo json_encode($ret);
    }

    public function testGetDeviceByUid()
    {
        $ret = $this->bus->user()->deviceId->getDeviceByUid(75746551);
        echo json_encode($ret);
    }

    public function testIsBoundDevice()
    {
        $ret = $this->bus->user()->deviceId->isBoundDevice("00129cceafcda405",75746551);
        echo json_encode($ret);
    }

    public function testIsNewDevice()
    {
        $ret = $this->bus->user()->deviceId->isNewDevice("023112A6-3F82-44D7-9B63-25603E17687C");
        echo json_encode($ret);
    }

    public function testGetExtraInfo()
    {
        $ret = $this->bus->user()->user->getPreference(10380006,["autoAttention"]);
        echo json_encode($ret);
    }

    public function testUpdateExtraInfo()
    {
        $ret = $this->bus->user()->user->setPreference(10380006,"autoAttention",0);
        echo json_encode($ret);
    }

    public function testGetLastPayMethod()
    {
        $ret = $this->bus->user()->user->getLastPayMethod(10380006);
        echo json_encode($ret);
    }

    public function testUpdateLastPayMethod()
    {
        $ret = $this->bus->user()->user->updateLastPayMethod(10380006,"wechat");
        echo json_encode($ret);
    }

    public function testUpdateBalance()
    {
        $ret = $this->bus->user()->user->updateBalance(8610091,10,10);
        echo json_encode($ret);
    }

    public function testUpdateBail()
    {
        $ret = $this->bus->user()->user->updateBail(10380006,10);
        echo json_encode($ret);
    }

    public function testGetBnpJson()
    {
        $ret = $this->bus->user()->user->getBnpJson(10380006);
        echo json_encode($ret);
    }

    public function testUpdateBnpJson()
    {
        $ret = $this->bus->user()->user->updateBnpJson(10380006,"[\"all\"]");
        echo json_encode($ret);
    }

    public function testUpdateSellerLevelScores()
    {
        $ret = $this->bus->shop()->shop->updateSellerLevelScores(10380006,10);
        echo json_encode($ret);
    }

    public function testUpdateScene()
    {
        $ret = $this->bus->user()->user->updateScene(10380006,"1234565");
        echo json_encode($ret);
    }

    public function testGetDeliveryCom()
    {
        $ret = $this->bus->shop()->shopSetting->getDeliveryCom(8700455);
        echo json_encode($ret);
    }

    public function testSetDeliveryCom()
    {
        $ret = $this->bus->shop()->shopSetting->setDeliveryCom(8700455,["adfdfd","dafdfae"]);
        echo json_encode($ret);
    }

    public function testGetRiskList()
    {
        $ret = $this->bus->user()->user->getRiskList(8610313,2);
        echo json_encode($ret);
    }

    public function testGetRiskListIds()
    {
        $ret = $this->bus->user()->user->getRiskListIds(0);
        echo json_encode($ret);
    }

    public function testGetCountByIdCode()
    {
        $ret = $this->bus->user()->user->getCountByIdCode("33072619960526253X");
        echo json_encode($ret);
    }

    public function testGetAttentionNum()
    {
        $ret = $this->bus->user()->friend->getAttentionNum(8622493);
        echo json_encode($ret);
    }

    public function testGetAttentionInfo()
    {
        $ret = $this->bus->user()->friend->getAttentionInfo(8622493,10000);
        echo json_encode($ret);
    }

    public function testGetAttentionInfoBatch()
    {
        $ret = $this->bus->user()->friend->getAttentionInfoBatch(8622493,[10000]);
        echo json_encode($ret);
    }

    public function testGetAttentionShopIdAllList()
    {
        $ret = $this->bus->user()->friend->getAttentionShopIdAllList(8622493);
        echo json_encode($ret);
    }

    public function testUpdateAttention()
    {
        $ret = $this->bus->user()->friend->updateAttention(8622493,10000,0,"aaa");
        echo json_encode($ret);
    }

    public function testUpdateAttentionBatch()
    {
        $data = array(
            array('uid' => 8622493, 'shopId' => 10000, 'isAttention' => 1, 'source' => "aaa",)
        );
        $ret = $this->bus->user()->friend->updateAttentionBatch($data);
        echo json_encode($ret);
    }

    public function testUpdateDealNum()
    {
        $ret = $this->bus->user()->friend->updateDealNum(8622493,10000,1);
        echo json_encode($ret);
    }

    public function testGetAttentionShopSaleIdList()
    {
        $ret = $this->bus->user()->friend->getAttentionShopSaleIdList(8622493,10,"",false);
        echo json_encode($ret);
    }
}