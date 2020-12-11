<?php

namespace PayCenter\Tests;

use PayCenter\Config;

class ConfigTest extends TestCase
{
    public function testConfig()
    {
        $this->assertNotEmpty(Config::getHost());
        $this->assertNotEmpty(Config::getKey());
        $this->assertNotEmpty(Config::getProduct());
    }

    public function testSetNewConfig()
    {
        $newHost = 'http://example.com';
        $newKey = 'testkey';
        $newProduct = rand(1, 99);

        Config::set($newProduct, $newKey);
        $this->assertEquals($newKey, Config::getKey());
        $this->assertEquals($newProduct, Config::getProduct());
        $this->assertNotEmpty(Config::getHost());

        Config::set($newProduct, $newKey, $newHost);
        $this->assertEquals($newHost . '/', Config::getHost());

        Config::restore();
        $this->assertNotEmpty(Config::getHost());
        $this->assertNotEmpty(Config::getKey());
        $this->assertNotEmpty(Config::getProduct());
    }
}
