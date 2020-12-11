<?php


namespace App\Service\Example;

use App\Contracts\Example\IExampleService;
use App\Service\Example\Command\ExampleCommand;
use Illuminate\Support\ServiceProvider;

class ExampleServiceProvider extends ServiceProvider
{

    /**
     * @var array 命令集合
     */
    protected $commands = [
        ExampleCommand::class
    ];

    public function register()
    {
        $this->app->singleton(IExampleService::class, function () {
            return new ExampleManager();
        });

        $this->commands($this->commands);
    }

}