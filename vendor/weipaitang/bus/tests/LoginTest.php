<?php


namespace Tests;

use WptBus\Bus;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    protected $token = "eyJhbGciOiJIUzI1NiIsImtpZCI6InVzZXJ8MjAyMC0wOS0xNCIsInR5cCI6IkpXVCJ9.eyJ1cmkiOiIxODA0MjAxOTEweDdENWdkIiwic2NvcGVUeXBlIjoiIiwicm9sZSI6ImJhc2UiLCJpc1NpbmdsZVNpZ25PbiI6ZmFsc2UsInBsYXRmb3JtSWQiOjAsIm9wZW5pZCI6IiIsImlzTmV3IjpmYWxzZSwiaXNGaXJzdFJlZ2lzdGVyIjpmYWxzZSwidGVsZXBob25lIjoiIiwicGxhdGZvcm1JbmZvIjp7InBsYXRmb3JtSWQiOjAsIm9yaWdpbmFsVWlkIjowLCJvcmlnaW5hbFVyaSI6IiIsIm9wZW5pZCI6IiJ9LCJhdWQiOiIwIiwiZXhwIjoxNjAwNzU5NjkwLCJpYXQiOjE2MDAxNTQ4OTAsImlzcyI6IndwdCIsIm5iZiI6MTYwMDE1NDg5MCwic3ViIjoiYmFzZSJ9.x5usVhAHc0wErNfO5RSHlpAOA8ePYw8XlELFdxPQX-Y";
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
                    ]
                ]
            ]
        ];

        $this->bus = new \WptBus\Bus($this->config);
    }

    public function testLogin()
    {
        $data = [
            "verifyType" => "code",
            "nationCode" => "86",
            "telephone" => "15088751042",
            "code" => "5179"
        ];
        $ret = $this->bus->user()->login->login(41, $data);
        echo json_encode($ret);
    }

    public function testAuthenticate()
    {
        $ret = $this->bus->user()->login->authenticate(true, ["token" => $this->token]);
        var_dump($ret);
        echo json_encode($ret);
    }

    public function testRefreshToken()
    {
        $ret = $this->bus->user()->login->refreshToken($this->token);
        echo json_encode($ret);
    }

    public function testGetLoginLogList()
    {
        $ret = $this->bus->user()->login->getLoginLogList(11621581);
        echo json_encode($ret);
    }

    public function testManualOneClickVerify()
    {
        $ret = $this->bus->user()->userVerify->manualOneClickVerify(11700010,"17600223175");
        echo json_encode($ret);
    }

    public function testCreateCode()
    {
        $ret = $this->bus->user()->login->createCode();
        echo json_encode($ret);
    }

    public function testGetCode()
    {
        $ret = $this->bus->user()->login->getCode("c0817b0d8b8d4e1bbddf35adb0dafd0f");
        echo json_encode($ret);
    }

    public function testCheckCode()
    {
        $ret = $this->bus->user()->login->checkCode(10,"c0817b0d8b8d4e1bbddf35adb0dafd0f");
        echo json_encode($ret);
    }

    public function testConfirmCode()
    {
        $ret = $this->bus->user()->login->confirmCode(10,"c0817b0d8b8d4e1bbddf35adb0dafd0f");
        echo json_encode($ret);
    }

    public function testCancelCode()
    {
        $ret = $this->bus->user()->login->cancelCode(10,"c0817b0d8b8d4e1bbddf35adb0dafd0f");
        echo json_encode($ret);
    }
}