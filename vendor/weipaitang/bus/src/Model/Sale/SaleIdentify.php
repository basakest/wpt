<?php

namespace WptBus\Model\Sale;

use WptBus\Lib\Utils;

class SaleIdentify implements \JsonSerializable
{

    private $identify;

    public function __construct($identify)
    {
        $this->identify = $identify;
    }

    public function __get($name)
    {
        return Utils::get_property($this->identify, $name);
    }

    public function __set($name, $value)
    {
        $this->identify->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->identify->$name);
    }

    public function __unset($name)
    {
        unset($this->identify->$name);
    }

    public function jsonSerialize()
    {
        return collect($this->identify)->toArray();
    }
}
