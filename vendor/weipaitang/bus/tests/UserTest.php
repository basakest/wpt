<?php

namespace Tests\Service\User\Module;

use WptBus\Bus;
use WptBus\Service\User\Module\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
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
                        'http://127.0.0.1:8080/',
                    ]
                ]
            ]
        ];

        $this->bus = new \WptBus\Bus($this->config);
    }

    public function testGetPrivacyInfoBatch()
    {
        $ret = $this->bus->user()->user->getPrivacyInfoBatch([8611652, 174], ["telephone", "name",  "idCode"]);
        echo json_encode($ret);
    }
}
