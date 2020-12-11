<?php

namespace WptBus\Model\Sale;

use WptBus\Lib\Utils;

/**
 * Class SaleShare
 * @property shareTitle
 * @property shareDesc
 * @property shareSummary
 * @package WptBus\Model\Sale
 */
class SaleShare implements \JsonSerializable
{
    private $share;

    public function __construct($share)
    {
        $this->share = $share;
    }

    public function __get($name)
    {
        return Utils::get_property($this->share, $name);
    }

    public function __set($name, $value)
    {
        $this->share->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->share->$name);
    }

    public function __unset($name)
    {
        unset($this->share->$name);
    }

    public function jsonSerialize()
    {
        return collect($this->share)->toArray();
    }
}
