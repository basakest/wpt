<?php

namespace WptBus\Model\Sale;

use WptBus\Lib\Utils;
use WptCommon\Library\Facades\MLogger;

class SaleProfile implements \JsonSerializable
{
    private $profile;
    private $from;

    public function __construct($profile, $from)
    {
        $this->profile = $profile;
        $this->from = $from;
    }

    public function __get($name)
    {
        MLogger::info("sale_profile_dig", 'get', ['info' => $name, 'origin' => $this->from], true);
        return Utils::get_property($this->profile, $name);
    }


    public function __set($name, $value)
    {
        MLogger::info("sale_profile_dig", 'set', ['info' => $name, 'origin' => $this->from], true);
        $this->profile->$name = $value;
    }

    public function __isset($name)
    {
        MLogger::info("sale_profile_dig", 'isset', ['info' => $name, 'origin' => $this->from], true);
        return isset($this->profile->$name);
    }

    public function __unset($name)
    {
        MLogger::info("sale_profile_dig", 'unset', ['info' => $name, 'origin' => $this->from], true);
        unset($this->profile->$name);
    }

    public function jsonSerialize()
    {
        MLogger::info("sale_profile_dig", 'json_encode', ['info' => 'dump_profile', 'origin' => $this->from], true);
        return collect($this->profile)->toArray();
    }
}
