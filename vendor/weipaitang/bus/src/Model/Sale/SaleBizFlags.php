<?php

namespace WptBus\Model\Sale;

use WptBus\Lib\Utils;

/**
 * Class SaleBizFlags
 *  @property $isOfficial
 *  @property $isFirstEnd
 *  @property $isLiveSale
 *  @property $isHidden
 *  @property $republished
 *  @property $isPunish
 *  @property $isRecommendShopSale
 *  @property $isUnitary
 *  @property $isNoviceCheap
 *  @property $preSell
 *  @property $isWptUnion
 *  @package WptBus\Model\Sale
 */


class SaleBizFlags implements \JsonSerializable
{
    private $bizFlags;

    public function __construct($bizFlags)
    {
        $this->bizFlags = $bizFlags;
    }

    public function __get($name)
    {
        return Utils::get_property($this->bizFlags, $name);
    }

    public function __set($name, $value)
    {
        $this->bizFlags->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->bizFlags->$name);
    }

    public function __unset($name)
    {
        unset($this->bizFlags->$name);
    }

    public function jsonSerialize()
    {
        return collect($this->bizFlags)->toArray();
    }
}
