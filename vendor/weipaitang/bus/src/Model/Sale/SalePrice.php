<?php

namespace WptBus\Model\Sale;

use WptBus\Lib\Utils;

class SalePrice implements \JsonSerializable
{
    private $SalePrice;

    public function __construct($SalePrice)
    {
        $this->SalePrice = $SalePrice;
    }

    public function __get($name)
    {
        return Utils::get_property($this->SalePrice, $name);
    }

    public function __set($name, $value)
    {
        $this->SalePrice->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->SalePrice->$name);
    }

    public function __unset($name)
    {
        unset($this->SalePrice->$name);
    }

    public function jsonSerialize()
    {
        return collect($this->SalePrice)->toArray();
    }
}
