<?php

require_once __DIR__ . '/../vendor/autoload.php';

define('TRACE_ID', md5('msg-api' . uniqid() . rand(100000, 999999)));

/*
|--------------------------------------------------------------------------
| 是否是CLI模式
|--------------------------------------------------------------------------
*/
define('IS_CLI', PHP_SAPI == 'cli');

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__ . '/../')
);

$app->withFacades();
$app->withEloquent();
/*
|--------------------------------------------------------------------------
| 加载应用配置文件
|--------------------------------------------------------------------------
*/
$app->configure('app');
$app->configure('user');
$app->configure('common-library');
$app->configure('bus');


/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    App\Http\Middleware\AfterMiddleware::class,
    App\Http\Middleware\OperationLogMiddleware::class
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// 框架相关服务
$app->register(Illuminate\Redis\RedisServiceProvider::class);
$app->register(Laravel\Lumen\Console\ConsoleServiceProvider::class);
$app->register(Laravel\Lumen\Providers\EventServiceProvider::class);

// 微拍堂通用服务
$app->register(WptCommon\Library\CommonLibraryProvider::class);
$app->register(WptBus\BusProvider::class);

// 业务相关服务
$app->register(App\Service\User\UserServiceProvider::class);
$app->register(App\Service\Example\ExampleServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->routeMiddleware([
    'user' => App\Http\Middleware\LoginMiddleware::class,
    'auth' => App\Http\Middleware\PermissionMiddleware::class,
    'profile' => App\Http\Middleware\ProfileMiddleware::class,
]);
$app->router->group([
    'namespace' => '\App\Http\Api\Controllers',
    'prefix' => '/api/',
    'middleware' => ["profile"],
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

return $app;
