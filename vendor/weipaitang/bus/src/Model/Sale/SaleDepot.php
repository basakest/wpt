<?php

namespace WptBus\Model\Sale;

use WptBus\Lib\Utils;

/**
 * Class SaleDepot
 * @property $depotId
 * @property $depotUserId
 * @property $depotPdId
 * @property $depotPrId
 * @property $depotCategory
 * @property $depotSecCategory
 * @property $depotPdPrice
 * @property $pdId
 * @property $libId
 * @property $masterUserinfoId
 * @property $operatorUserinfoId
 * @property $settleRatio
 * @property $depotCommission
 * @package WptBus\Model\Sale
 */
class SaleDepot implements \JsonSerializable
{
    private $depot;

    public function __construct($depot)
    {
        $this->depot = $depot;
    }

    public function __get($name)
    {
        return Utils::get_property($this->depot, $name);
    }

    public function __set($name, $value)
    {
        $this->depot->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->depot->$name);
    }

    public function __unset($name)
    {
        unset($this->depot->$name);
    }

    public function jsonSerialize()
    {
        return collect($this->depot)->toArray();
    }
}
