<?php


use WptUtils\TimeUtil;

class TimeTest extends \PHPUnit\Framework\TestCase
{
    public function testTime()
    {

        var_dump(TimeUtil::convertDateSignToUnix("today"));

        var_dump(TimeUtil::isFriday());

        var_dump(TimeUtil::now());
        echo TimeUtil::getMillisecond();
        echo PHP_EOL;
        echo TimeUtil::timeStyle(time() + 100);
        die;
    }


}