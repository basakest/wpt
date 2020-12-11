<?php

namespace WptCommon\Library\Tests;

use Illuminate\Support\Str;
use PHPUnit\Framework\Exception;
use WptCommon\Library\Consts\AlertTypes;
use WptCommon\Library\Tools\Logger;

class TestCase extends BaseTestCase
{
    public function test_info()
    {
        Logger::getInstance()->info($this->filePrefix, $this->message);
        $log = $this->getLastLog();
        $this->assertEquals('info', $log->level);
        $this->assertEquals($this->message, $log->message);
    }

    public function test_warning()
    {
        Logger::getInstance()->warning($this->filePrefix, $this->message);
        $log = $this->getLastLog();

        $this->assertEquals('warning', $log->level);
        $this->assertEquals($this->message, $log->message);
    }

    public function test_error()
    {
        Logger::getInstance()->error($this->filePrefix, $this->message);
        $log = $this->getLastLog();
        $this->assertEquals('error', $log->level);
        $this->assertEquals($this->message, $log->message);
    }

    public function test_exception()
    {
        $ex = new Exception($this->message);
        Logger::getInstance()->exception($this->filePrefix, $ex);
        $log = $this->getLastLog();
        $this->assertEquals('critical', $log->level);
        $this->assertEquals($this->message, $log->message);
        $this->setRandomMessage();
        $ex = new \Error($this->message);
        Logger::getInstance()->exception($this->filePrefix, $ex);
        $log = $this->getLastLog();
        $this->assertEquals('critical', $log->level);
        $this->assertEquals($this->message, $log->message);

    }

    public function test_alert_type()
    {
        Logger::getInstance()->error($this->filePrefix, $this->message);
        $log = $this->getLastLog();
        $this->assertEquals(implode('|', [AlertTypes::ALERT_DING, AlertTypes::ALERT_MAIL]), $log->alert);
        Logger::getInstance()->error($this->filePrefix, $this->message, [], false, AlertTypes::ALERT_DING);
        $log = $this->getLastLog();
        $this->assertEquals(AlertTypes::ALERT_DING, $log->alert);
    }

    public function test_content()
    {
        $content = ['xx' => 1, 'yy' => ['xxxx' => '大家好']];
        Logger::getInstance()->error($this->filePrefix, $this->message, $content);
        $this->assertEquals(json_encode($content, JSON_UNESCAPED_UNICODE), $this->getLastLog()->content);
    }


    public function test_expand()
    {
        $content = ['xx' => 1, 'yy' => ['xxxx' => '大家好'], 'id' => 12323123, 'price' => ['xx' => 1, 'uu' => 1.1]];
        $expand = ['id' => 12323123, 'price' => ['xx' => 1, 'uu' => 1.1]];
        Logger::getInstance()->error($this->filePrefix, $this->message, $content, true);
        $this->assertEquals(json_encode($content, JSON_UNESCAPED_UNICODE), $this->getLastLog()->content);
        $this->assertEquals(json_decode(json_encode($expand)), $this->getLastLog()->expand);
    }


    public function test_content_obj()
    {
        $content = ['xx' => 1, 'yy' => ['xxxx' => '大家好11'], 'id' => 12323123, 'price' => ['xx' => 1, 'uu' => 1.1,], 'unique_id' => 3123123];
        $content = json_decode(json_encode($content));
        Logger::getInstance()->error($this->filePrefix, $this->message, $content, true);

        $this->assertEquals(json_encode($content, JSON_UNESCAPED_UNICODE), $this->getLastLog()->content);
        $this->assertNull($this->getLastLog()->expand ?? null);

        $this->assertEquals($content->unique_id, $this->getLastLog()->unique_id);
    }

    public function test_unique_id()
    {
        $uniqueId = Str::random();
        $content = ['xx' => 1, 'yy' => ['xxxx' => '大家好'], 'id' => 12323123, 'price' => ['xx' => 1, 'uu' => 1.1111], 'unique_id' => $uniqueId];
        $expand = ['id' => 12323123, 'price' => ['xx' => 1, 'uu' => 1.1111]];
        Logger::getInstance()->error($this->filePrefix, $this->message, $content, true);
        $this->assertEquals(json_encode($content, JSON_UNESCAPED_UNICODE), $this->getLastLog()->content);
        $this->assertEquals(json_decode(json_encode($expand)), $this->getLastLog()->expand);
        $this->assertEquals($uniqueId, $this->getLastLog()->unique_id);
    }

    public function test_refresh_config()
    {
        $content = ['xx' => 1, 'yy' => ['xxxx' => '大家好'], 'id' => 12323123111, 'price' => ['xx' => 1, 'uu' => 1.1]];
        $expand = ['id' => 12323123111, 'price' => ['xx' => 1, 'uu' => 1.1]];
        Logger::getInstance()->info($this->filePrefix, $this->message, $content, true);
        $this->assertEquals(json_decode(json_encode($expand)), $this->getLastLog()->expand);

        $config = ['mlogger' => ['log_level' => 'info', 'logs_dir' => 'newlogs', 'expand_fields' => []]];
        Logger::getInstance($config)->info($this->filePrefix, $this->message, $content, true);
        $this->assertEquals(json_decode(json_encode($expand)), $this->getLastLog()->expand);

        Logger::getInstance($config, true)->info($this->filePrefix, $this->message, $content, true);
        $expand = ['price' => ['xx' => 1, 'uu' => 1.1]];
        $this->assertEquals(json_decode(json_encode($expand)), $this->getLastLog()->expand);
    }

}
