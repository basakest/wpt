<?php

namespace WptBus\Model\Sale;

use WptBus\Lib\Utils;

class SaleFee implements \JsonSerializable
{
    private $fee;

    public function __construct($fee)
    {
        $this->fee = $fee;
    }

    public function __get($name)
    {
        return Utils::get_property($this->fee, $name);
    }

    public function __set($name, $value)
    {
        $this->fee->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->fee->$name);
    }

    public function __unset($name)
    {
        unset($this->fee->$name);
    }

    public function jsonSerialize()
    {
        return collect($this->fee)->toArray();
    }
}
