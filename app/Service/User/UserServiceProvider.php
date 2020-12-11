<?php


namespace App\Service\User;

use App\Contracts\User\IUserService;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(IUserService::class, function () {
            return new UserManager();
        });
    }
}