<?php

namespace WptBus\Model\Sale;

use WptBus\Lib\Utils;

/**
 * @property $applyId
 * @property $applySaleId
 * @property $periodUri
 * @property $discount
 * @property $activityCate
 * @property $activityCode
 */

class SaleActivity implements \JsonSerializable
{
    private $activity;

    public function __construct($activity)
    {
        $this->activity = $activity;
    }

    public function __get($name)
    {
        return Utils::get_property($this->activity, $name);
    }

    public function __set($name, $value)
    {
        $this->activity->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->activity->$name);
    }

    public function __unset($name)
    {
        unset($this->activity->$name);
    }

    public function jsonSerialize()
    {
        return collect($this->activity)->toArray();
    }
}
