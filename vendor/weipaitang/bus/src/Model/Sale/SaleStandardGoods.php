<?php

namespace WptBus\Model\Sale;

use WptBus\Lib\Utils;

class SaleStandardGoods implements \JsonSerializable
{
    private $standardGoods;

    public function __construct($standardGoods)
    {
        $this->standardGoods = $standardGoods;
    }

    public function __get($name)
    {
        return Utils::get_property($this->standardGoods, $name);
    }

    public function __set($name, $value)
    {
        $this->standardGoods->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->standardGoods->$name);
    }

    public function __unset($name)
    {
        unset($this->standardGoods->$name);
    }

    public function jsonSerialize()
    {
        return collect($this->standardGoods)->toArray();
    }
}
