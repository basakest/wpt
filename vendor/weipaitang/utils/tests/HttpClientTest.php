<?php


namespace WptUtilsTest;

use PHPUnit\Framework\TestCase;
use WptUtils\Exception\HttpException;
use WptUtils\Http\Client;

class HttpClientTest extends TestCase
{
    public function testCurl()
    {

        print_r(Client::instance()->setTimeout(1000));
        print_r(Client::instance()->get("http:"));
        print_r(Client::instance());
        print_r(Client::instance());
        print_r(Client::instance());
        die;

        // $curl = new Client();
        // $curl->setHeader("x-blog", 100);
        // $curl->setHeader("x-code", 200);
        // $curl->setCookie("userinfo_cookie", 'OKXJSotYA2h3t0AnCFpy4JjpZCFs3sdOSFbKT4qXDfBBgc2re5D5qI4eyvJG0xsb2WkHPgX' .
        //     '%2BMqR5KjaP2btlCVsVSci0H%2BcomSZzWZJ%2BGQP8naMFrGZtEAyfywVaw0Ux');
        // $curl->setCookie("a", "10");
        // $curl->setTimeout(1000);
        // $curl->setConnectTimeout(1000);
        // $curl->setReferer("http://www.baidu.com");
        // $curl->asJson()->setRetries(0);
        // var_dump($curl->get("http://127.0.0.1:3002?a=1&b=2&c=_c122")->getResponse());

        $client = new Client();
        $client->get("http://www.baidu.com")->getResponse();
        $client->get("http://www.baidu.com");
        $client->get("http://www.baidu.com");
        $client->get("http://www.baidu.com");


        try {
            $p1 = (new Client())->setHeader("a", "b")->setProxy("127.0.0.1", 1087)->get("https://www.google.com/");
            print_r($p1->getResponse());
        } catch (HttpException $e) {
            var_dump($e->getMessage());
        }

        die;
    }

    public function testMultiHttp()
    {
        try {
            $client = new Client();
            $client->add(function (Client $client) {
                $client->setHeader("a", "1");
                $client->get("http://www.baidu.com");
            });
            $client->add(function (Client $client) {
                $client->setHeader("b", "2");
                $client->get("http://www.baidu.com");
            });
            $client->add(function (Client $client) {
                $client->setHeader("c", "3");
                $client->get("http://www.baidu.com");
            });

            var_dump($client->start()->getResponse());
        } catch (HttpException $exception) {
            var_dump($exception->getMessage());
        }

        try {
            $client = new Client();
            $client->start()->getResponse();
        } catch (\Throwable $e) {
            var_dump($e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function testInvoke()
    {
        try {
            $result = (new Client())->get("http://www.baidu.com")->getResponse();
            var_dump($result);

        } catch (HttpException $e) {
            var_dump($e->getMessage());
        }

        die;
    }

    public function testMultiReq()
    {
        $texts = [
            [
                't' => '我是文字',
                'font' => 'pingfang_regular',
                'size' => 36,
            ],
            [
                't' => '我是内容哈哈哈',
                'font' => 'pingfang_sc_semibold',
                'size' => 36,
            ],
            [
                't' => 'abcdasdf',
                'font' => 'pingfang_regular',
                'size' => 36,
            ],
            [
                't' => '我是标题我是标题我是标题我是标题我是标题',
                'font' => 'pingfang_sc_semibold',
                'size' => 30,
            ]
        ];

        $res = $this->mg($texts);
        print_r($res);
        die;
    }

    private function mg($texts)
    {
        $client = new Client();
        foreach ($texts as $k => $text) {
            $client->add(function (Client $client) use ($text, $k) {
                $textrq = [
                    't' => $text['t'],
                    'font' => $text['font'],
                    'size' => $text['size']
                ];
                $client->setHeader('text', md5($text['t']));
                $client->get("http://draw.weipaitang.com/gd/text/width", $textrq);
            });
        }

        $result = $client->setKey('text')->start()();
        return $result;

    }
}
