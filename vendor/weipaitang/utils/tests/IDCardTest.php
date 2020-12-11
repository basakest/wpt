<?php


class IDCardTest extends \PHPUnit\Framework\TestCase
{
    public function testID()
    {
        $exp = \WptUtils\IDCard::validate(140721199901170633);

        $this->assertTrue($exp);
    }
}
