<?php

namespace WptUtilsTest;

use PHPUnit\Framework\TestCase;
use WptUtils\Codec\Base62;
use WptUtils\Str;

class UtilsTest extends TestCase
{
    public function testRun()
    {
        $this->assertTrue(true);
    }

    public function testStrings()
    {
        echo Str::uuid([1]);
        $this->assertTrue(true);
    }

    public function testParseUrl()
    {
        $actual = Str::parseUrl("https://api.weipaitang.com/app/v1.0?a=1");
        $expected = ['a' => 1];

        $this->assertEquals($actual, $expected);
    }

    public function testAddUrlParameter()
    {
        $url = Str::addUrlParameter("https://api.weipaitang.com/app/v1.0", ['a' => 1]);
        $expected = "https://api.weipaitang.com/app/v1.0?a=1";
        $this->assertEquals($url, $expected);
    }

    public function testSubStr()
    {
        $actual = Str::substr("123123", 3, 'abc');
        $expected = "123...";
        $this->assertEquals($actual, $expected);
    }

    public function testConvertLineBreak()
    {
        $actual = Str::convertLineBreak("111\n222<br />333");
        echo $actual;
    }

    public function testHiddenNickName()
    {
        echo Str::removeSpecialSymbol("wo`zai|?-hahahha1f9[dcddd,");
    }

    public function testCodec()
    {
        echo Base62::encode(1939816);
        $this->assertTrue(true);
    }
}
