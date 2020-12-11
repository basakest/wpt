<?php

namespace WptBus\Service\Sale;

use WptBus\Service\BaseService;
use WptBus\Service\Sale\Module\Author;
use WptBus\Service\Sale\Module\Bid;
use WptBus\Service\Sale\Module\Goods;
use WptBus\Service\Sale\Module\Sale;
use WptBus\Service\Sale\Module\Normal;
use WptBus\Service\Sale\Module\SaleLike;
use WptBus\Service\Sale\Module\Recommend;
use WptBus\Service\Sale\Module\SaleComponent;
use WptBus\Service\Sale\Module\StandardGoods;
use WptBus\Service\Sale\Module\AdminPublisher;
use WptBus\Service\Sale\Module\Brand;
use WptBus\Service\Sale\Module\Category;
use WptBus\Service\Sale\Module\Draft;
use WptBus\Service\Sale\Module\Master;
use WptBus\Service\Sale\Module\Discovery;
use WptBus\Service\Sale\Module\Search;

/**
 * Class Application
 * @package WptBus\Service\Sale
 *
 * @property SaleComponent $saleComponent
 * @property Sale $sale
 * @property StandardGoods $standardGoods
 * @property Bid $bid
 * @property SaleLike $saleLike
 * @property Normal $normal
 * @property Recommend $recommend
 * @property AdminPublisher $adminPublisher
 * @property Brand $brand
 * @property Goods $goods
 * @property Author $author
 * @property Category $category
 * @property Draft $draft
 * @property Master $master
 * @property Discovery $discovery
 * @property Search $search
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
        'saleComponent' => SaleComponent::class,
        'sale' => Sale::class,
        'standardGoods' => StandardGoods::class,
        'bid' => Bid::class,
        'saleLike' => SaleLike::class,
        'normal' => Normal::class,
        'recommend' => Recommend::class,
        'adminPublisher' => AdminPublisher::class,
        'brand' => Brand::class,
        'goods' => Goods::class,
        'author' => Author::class,
        "category" => Category::class,
        'draft' => Draft::class,
        'master' => Master::class,
        'discovery' => Discovery::class,
        'search' => Search::class,
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
