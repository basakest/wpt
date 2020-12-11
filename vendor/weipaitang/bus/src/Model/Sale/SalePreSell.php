<?php

namespace WptBus\Model\Sale;

use WptBus\Lib\Utils;

/**
 * Class SalePreSell
 *
 *  @property preSell
 *  @property preSellDesc
 *  @property depositDeliveryDeadlineType
 *  @property depositDeliveryDeadline
 *  @property preSellMaxDeposit
 *  @property tailPayDeadline
 *  @property tailPayDeadlineType
 *  @property preSellRatio
 *  @property sale_open_time
 *  @property sale_end_time
 *  @property virtualDiscount
 * @package WptBus\Model\Sale
 */
class SalePreSell implements \JsonSerializable
{
    private $preSell;

    public function __construct($preSell)
    {
        $this->preSell = $preSell;
    }

    public function __get($name)
    {
        return Utils::get_property($this->preSell, $name);
    }

    public function __set($name, $value)
    {
        $this->preSell->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->preSell->$name);
    }

    public function __unset($name)
    {
        unset($this->preSell->$name);
    }

    public function jsonSerialize()
    {
        return collect($this->preSell)->toArray();
    }
}
