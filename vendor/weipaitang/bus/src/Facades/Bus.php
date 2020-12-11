<?php


namespace WptBus\Facades;

use WptBus\Service\Order\Application;
use Illuminate\Support\Facades\Facade;

/**
 * Class Bus
 * @package WptBus\Facades
 * @method static \WptBus\Service\User\Application user
 * @method static Application order
 * @method static \WptBus\Service\Sale\Application sale
 * @method static \WptBus\Service\Recommend\Application recommend
 * @method static \WptBus\Service\Shop\Application shop
 */
class Bus extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'bus';
    }
}