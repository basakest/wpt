<?php


namespace WptBus;

use WptBus\Service\BaseService;
use WptBus\Service\Order\Application;

/**
 * Class Bus
 * @package WptBus
 * @method Application order
 * @method \WptBus\Service\User\Application user
 * @method \WptBus\Service\Sale\Application sale
 * @method \WptBus\Service\Recommend\Application recommend
 * @method \WptBus\Service\Shop\Application shop
 */
class Bus
{
    private $config = [];

    private $build = [];

    public function __construct($config = [])
    {
        $this->initConfig($config);
    }

    public function initConfig($config)
    {
        foreach (glob(__DIR__ . "/Config/*.php", false) as $file) {
            $this->config[pathinfo($file, PATHINFO_FILENAME)] = include_once "$file";

        }
        foreach ($this->config as $serviceName => $defalutConfig) {
            if (isset($config[$serviceName])) {
                $defalutConfig["http"] = array_merge($defalutConfig["http"], $config[$serviceName]["http"] ?? []);
                $this->config[$serviceName] = $defalutConfig;
            }
        }
    }

    public function make($name, $arguments)
    {
        if (empty($this->build[$name])) {
            $namespace = ucfirst($name);
            $serviceClass = "\\WptBus\\Service\\{$namespace}\\Application";
            /** @var BaseService $app */
            $app = new $serviceClass($arguments);
            $app->init($name, $this->config[$name]);
            $this->build[$name] = $app;
        }
        return $this->build[$name];
    }

    public function __call($name, $arguments)
    {
        return $this->make($name, $arguments);
    }
}