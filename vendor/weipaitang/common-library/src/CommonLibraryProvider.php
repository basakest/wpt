<?php


namespace WptCommon\Library;


use Illuminate\Support\ServiceProvider;
use WptCommon\Library\Tools\Logger;

class CommonLibraryProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../config/common-library.php", "common-library");

        $this->app->singleton('mlogger', function ($app) {
            return new Logger($app['config']['common-library']);
        });
    }

}