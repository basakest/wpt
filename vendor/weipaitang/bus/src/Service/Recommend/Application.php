<?php


namespace WptBus\Service\Recommend;


use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Lib\Validator;
use WptBus\Service\BaseService;
use WptBus\Service\Recommend\Module\ShopMessage;

/**
 * Class Application
 * @package WptBus\Service\Recommend\ShopMessage
 * @property ShopMessage shopMessage
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
        'shopMessage' => ShopMessage::class,
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