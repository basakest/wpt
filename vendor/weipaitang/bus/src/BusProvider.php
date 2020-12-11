<?php


namespace WptBus;


use Illuminate\Support\ServiceProvider;

class BusProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('bus', function ($app) {
            return new Bus($app['config']['bus']);
        });
    }
}