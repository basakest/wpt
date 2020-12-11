<?php


namespace WptBus\Service\Order;


use WptBus\Service\BaseService;
use WptBus\Service\Order\Module\AfterSale;
use WptBus\Service\Order\Module\Buyer;
use WptBus\Service\Order\Module\BuyerRate;
use WptBus\Service\Order\Module\Delivery;
use WptBus\Service\Order\Module\OrderSearch;
use WptBus\Service\Order\Module\QuickDelivery;
use WptBus\Service\Order\Module\Refund;
use WptBus\Service\Order\Module\Restored;
use WptBus\Service\Order\Module\Seller;
use WptBus\Service\Order\Module\SellerRate;
use WptBus\Service\Order\Module\WorkRate;
use WptBus\Service\Order\Module\Order;
use WptBus\Service\Order\Module\DeductScore;
use WptBus\Service\Order\Module\OrderReturn;

/**
 * Class Application
 * @package WptBus\Service\Order
 * @property SellerRate sellerRate
 * @property BuyerRate buyerRate
 * @property WorkRate workRate
 * @property Order order
 * @property OrderSearch orderSearch
 * @property Delivery delivery
 * @property QuickDelivery quickDelivery
 * @property DeductScore deductScore
 * @property Refund refund
 * @property OrderReturn orderReturn
 * @property AfterSale afterSale
 * @property Restored restored
 * @property Buyer buyer
 * @property Seller seller
 */
class Application
{
    protected $serivceName;
    protected $config = [];

    public function init($serviceName, $config)
    {
        $this->serivceName = $serviceName;
        $this->config = $config;
    }

    protected $register = [
        'sellerRate' => SellerRate::class,
        'buyerRate' => BuyerRate::class,
        'workRate' => WorkRate::class,
        'order' => Order::class,
        'orderSearch' => OrderSearch::class,
        'delivery' => Delivery::class,
        'quickDelivery' => QuickDelivery::class,
        'deductScore' => DeductScore::class,
        'refund' => Refund::class,
        'orderReturn' => OrderReturn::class,
        'afterSale'=> AfterSale::class,
        'restored' => Restored::class,
        "buyer" => Buyer::class,
        "seller" => Seller::class,
    ];

    protected $build;

    public function __get($name)
    {
        if (empty($this->build[$name])) {
            /** @var BaseService $app */
            $app = new $this->register[$name]();
            $app->init($this->serivceName, $this->config);
            $this->build[$name] = $app;
        }
        return $this->build[$name];
    }
}