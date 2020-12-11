<?php

namespace WptBus\Model\Sale;

use WptBus\Lib\Utils;

/**
 * Class SaleCustomProps
 * @property $activityUri
 * @property $isAuctionHouse
 * @property int $isNotice
 * @package WptBus\Model\Sale
 */

class SaleCustomProps implements \JsonSerializable
{
    private $customProps;

    public function __construct($customProps)
    {
        $this->customProps = $customProps;
    }

    public function __get($name)
    {
        return Utils::get_property($this->customProps, $name);
    }

    public function __set($name, $value)
    {
        $this->customProps->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->customProps->$name);
    }

    public function __unset($name)
    {
        unset($this->customProps->$name);
    }

    public function jsonSerialize()
    {
        return collect($this->customProps)->toArray();
    }
}
