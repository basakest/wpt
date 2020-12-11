<?php


namespace WptOrder\OrderService;


use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../config/order.php", "order");

        $this->app->singleton('order', function ($app) {
            return OrderService::getInstance($app['config']['order']);
        });

        $this->commands([
            TestCommand::class
        ]);
    }

}