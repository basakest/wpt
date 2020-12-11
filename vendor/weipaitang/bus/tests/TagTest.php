<?php

namespace Tests\Service\User\Module;

use WptBus\Bus;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
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

    public function testCreate()
    {
        $ret = $this->bus->user()->tag->create(1, "user_wpt_5", "test-group-71", "", "");
        echo json_encode($ret);
    }

    public function testUpdate()
    {
        $ret = $this->bus->user()->tag->update(24, "user_wpt_5", "test-group-24-update", "", "");
        echo json_encode($ret);
    }

    public function testDelete()
    {
        $ret = $this->bus->user()->tag->delete(20, "user_wpt");
        echo $ret;
        echo json_encode($ret);

    }

    public function testCreateGroup()
    {
        $ret = $this->bus->user()->tag->createGroup("user_wpt_5", "user-group-71", "", "");
        echo json_encode($ret);
    }

    public function testUpdateGroup()
    {
        $ret = $this->bus->user()->tag->updateGroup(371, "user_wpt_5", "user-group-71-update", "", "");
        echo json_encode($ret);
    }

    public function testDeleteGroup()
    {
        $ret = $this->bus->user()->tag->deleteGroup(371, "user_wpt_5");
        echo $ret;
        echo json_encode($ret);

    }

    public function testChangeGroup()
    {
        $ret = $this->bus->user()->tag->changeGroup(11, 9, "user_wpt");
        echo json_encode($ret);
    }

    public function testBatchBind()
    {
        $ret = $this->bus->user()->tag->batchBind(8, "shop_wpt", [10000, 10010, 10100]);
        echo json_encode($ret);
    }

    public function testBatchUnbind()
    {
        $ret = $this->bus->user()->tag->batchUnBind(8, "shop_wpt", [10000, 10010, 10100]);
        echo json_encode($ret);
    }

    public function testBindListByEntityId()
    {
        $ret = $this->bus->user()->tag->bindListByEntityId("user_wpt_5", 100);
        var_dump($ret);
        echo json_encode($ret);
    }

    public function testTagListByBusinessUniqueId()
    {
        $ret = $this->bus->user()->tag->tagListByBusinessUniqueId("user_wpt_5");
        var_dump($ret);
        echo json_encode($ret);
    }

    public function testGetEntityIdsByTagId()
    {
        $ret = $this->bus->user()->tag->getEntityIdsByTagId(370, 0, 0);
        echo json_encode($ret);
    }

    public function testSync()
    {
        $ret = $this->bus->user()->tag->sync(82, 1, "test-tag-82", "user_wpt_5", "", "{\"a\":28}");
        echo json_encode($ret);
    }

    public function testSyncBatchBind()
    {
        $ret = $this->bus->user()->tag->syncBatchBind(88, 18989898, "user_wpt_5", [11111]);
        echo json_encode($ret);
    }

    public function testSyncBindTags()
    {
        $ret = $this->bus->user()->tag->syncBindTags(102, "user_wpt_5", "{\"31\":1031, \"32\":1032, \"33\":1033, \"39\":1039, \"ABC\":1234}");
        echo json_encode($ret);
    }

    public function testSyncUnbindTags()
    {
        $ret = $this->bus->user()->tag->syncUnbindTags(102, "user_wpt_5", [31, 32, 33, 88, 332, 357, 359]);
        echo json_encode($ret);
    }

}
