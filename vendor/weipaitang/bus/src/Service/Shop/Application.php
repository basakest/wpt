<?php


namespace WptBus\Service\Shop;

use WptBus\Service\BaseService;
use WptBus\Service\Shop\Module\Black;
use WptBus\Service\Shop\Module\Punish;
use WptBus\Service\Shop\Module\Shop;
use WptBus\Service\Shop\Module\ShopReport;
use WptBus\Service\Shop\Module\ShopSetting;
use WptBus\Service\Shop\Module\SubAccount;
use WptBus\Service\Shop\Module\Tag;

/**
 * Class Application
 * @package WptBus\Service\Shop
 * @property Shop shop
 * @property Punish punish
 * @property ShopSetting shopSetting
 * @property SubAccount subAccount
 * @property Tag tag
 * @property Black black
 * @property ShopReport shopReport
 */
class Application extends BaseService
{
    protected $serviceName;
    protected $config = [];
    protected $build;

    public function init($serviceName, $config)
    {
        $this->serviceName = $serviceName;
        $this->config = $config;
    }


    protected $register = [
        'shop' => Shop::class,
        'punish' => Punish::class,
        'shopSetting' => ShopSetting::class,
        'subAccount' => SubAccount::class,
        'tag' => Tag::class,
        'black' => Black::class,
        'shopReport' => ShopReport::class
    ];


    public function __get($name)
    {
        if (empty($this->build[$name])) {
            /** @var BaseService $app */
            $app = new $this->register[$name]();
            $app->init($this->serviceName, $this->config);
            $this->build[$name] = $app;
        }
        return $this->build[$name];
    }
}