<?php


namespace Tests;

use WptBus\Bus;
use PHPUnit\Framework\TestCase;


class AddressTest extends TestCase
{
    protected $config = [];
    /**
     * @var Bus
     */
    protected $bus;

    public function setUp()
    {
        defined('TRACE_NAME') || define('TRACE_NAME', 'api');
        defined('TRACE_HOST') || define('TRACE_HOST', ip2long('127.0.0.1'));
        defined('TRACE_ID') || define('TRACE_ID', md5(TRACE_NAME . TRACE_HOST . uniqid() . rand(100000, 999999)));

        $this->config = [
            'user' => [ // 服务名
                'http' => [ // http配置
                    'servers' => [
                       'http://10.3.7.34:8080/',
                       // 'http://172.16.34.198:8080/',
                    ]
                ]
            ]
        ];

        $this->bus = new \WptBus\Bus($this->config);
    }


    public function testGetAddress()
    {
        $ret = $this->bus->user()->address->getAddress(8700455,2870063);
        echo json_encode($ret);
    }

    public function testCreateAddress()
    {
        $ret = $this->bus->user()->address->createAddress(8700455,[
            "userName" => "江俊2",
            "proviceFirstStageName" => "北京市",
            "addressCitySecondStageName" => "北京市",
            "addressCountiesThirdStageName" => "昌平取",
            "addressDetailInfo" => "点点滴滴",
            "telNumber" => "15990058711"
        ]);
        echo json_encode($ret);
    }

    public function testUpdateAddress()
    {
        $ret = $this->bus->user()->address->updateAddress(8700455,2830315,[
            "userName" => "江俊2",
            "proviceFirstStageName" => "北京市",
            "addressCitySecondStageName" => "北京市",
            "addressCountiesThirdStageName" => "昌平取",
            "addressDetailInfo" => "点点滴232312滴",
            "telNumber" => "15990058711"
        ]);
        echo json_encode($ret);
    }

    public function testDeleteAddress()
    {
        $ret = $this->bus->user()->address->deleteAddress(8700455,2830315);
        echo json_encode($ret);
    }


    public function testCreateOrUpdateByUri()
    {
        $ret = $this->bus->user()->address->createOrUpdateByUri(8700455,0,[
            "userName" => "江俊2",
            "proviceFirstStageName" => "北京市",
            "addressCitySecondStageName" => "北京市",
            "addressCountiesThirdStageName" => "昌平取",
            "addressDetailInfo" => "点点滴232312滴",
            "telNumber" => "15990058711"
        ]);
        echo json_encode($ret);
    }

    public function testSetDefaultShippingAddress()
    {
        $ret = $this->bus->user()->address->SetDefaultShippingAddress(8700455,2830315);
        echo json_encode($ret);
    }

    public function testSetDefaultReturnAddress()
    {
        $ret = $this->bus->user()->address->SetDefaultReturnAddress(8700455,2830315);
        echo json_encode($ret);
    }

    public function testGetDefaultShippingAddress()
    {
        $ret = $this->bus->user()->address->GetDefaultShippingAddress(8700455);
        echo json_encode($ret);
    }

    public function testGetDefaultReturnAddress()
    {
        $ret = $this->bus->user()->address->GetDefaultReturnAddress(8700455);
        echo json_encode($ret);
    }

    public function testGetAddressList()
    {
        $ret = $this->bus->user()->address->GetList(8700455);
        echo json_encode($ret);
    }

}