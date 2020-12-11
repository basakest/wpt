<?php


class DesTest extends \PHPUnit\Framework\TestCase
{
    public function testDes()
    {
        $out = \WptUtils\Crypto::desCbcEncrypt("abc");
        $exptected = \WptUtils\Crypto::desCbcDecrypt($out);
        $this->assertEquals('abc', $exptected);

        echo \WptUtils\Crypto::desCbcEncrypt("123");

        die;
    }
}
