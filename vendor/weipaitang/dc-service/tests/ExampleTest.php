<?php

namespace Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use WptDataCenter\DataCenter;
use WptDataCenter\Handler\CurlHandler;
use WptDataCenter\PosterAdmin;

class ExampleTest extends TestCase
{
    public function setUp()
    {
        (Dotenv::create(__DIR__ . "/../", '.env'))->load();
    }

    /**
     *
     */
    public function testRun()
    {
        $this->assertTrue(true);
    }

    /**
     *
     */
    public function testCurl()
    {
        $result = CurlHandler::getInstance()->getEndpoint();
        print_r($result);
        $this->assertTrue(true);
    }

    public function testReport()
    {
        $result = [];
        $expected = [
            'last_15d_refund_num' => '10',
            'yesterday_receipt_num' => '20',
        ];
        try {
            $result = DataCenter::getInstance()->get(1999, ['last_15d_refund_num', 'yesterday_receipt_num']);
            print_r($result);
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }

        $this->assertEquals($result, $expected);
    }

    public function testSubscribeGet()
    {
        try {
            $result = DataCenter::getInstance()->getByDate(19891, ['group_buy_price'], '20191126');
            var_dump($result);
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }

        $this->assertTrue(true);

    }

    public function testMutliGet()
    {
        try {
            $result = DataCenter::getInstance()->dayRange(3, 4)->multiGetByDate(25382764, ['group_buy_price', 'group_buy_order']);
            print_r($result);
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }
        /*
         Array
        (
            [group_buy_price] => Array
                (
                    [20191126] => 8000
                    [20191125] => 216000
                )

            [group_buy_order] => Array
                (
                    [20191126] => 1999
                )

        )
         */

        $this->assertTrue(true);
    }

    public function testMuliGetAndCountByDate()
    {
        try {
            $days = [];
            for ($i = 0; $i < 5; $i++) {
                $days[] = date("Ymd", strtotime(-1 * $i . ' day'));
            }
            $result = DataCenter::getInstance()->dayRange(3, 4)->multiGetAndCountByDate(19191, ['paidNum', 'publishNum'], ['123123', '123123']);
            $result = DataCenter::getInstance()->today()->multiGetAndCountByDate(19191, ['paidNum', 'publishNum']);
            $result = DataCenter::getInstance()->yestoday()->multiGetAndCountByDate(19191, ['paidNum', 'publishNum']);
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }
        $this->assertTrue(true);
    }

    public function testPoster()
    {
        var_dump(PosterAdmin::getInstance()->preview(199));
    }
}
