<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/** @var Laravel\Lumen\Routing\Router $router */

use App\Http\Middleware\PermissionMiddleware;

/**
 * 用户相关路由定义
 */
$router->group(['prefix' => 'user'],function() use ($router) {
    $router->addRoute(['GET'], 'info', 'UserController@info');
    $router->addRoute(['GET'], 'test', 'UserController@test');
});


/**
 * 需鉴权接口
 */
$router->group(['prefix' => 'example', 'middleware' => ["user", "auth"]],function() use ($router) {
    $router->addRoute(['GET'], '/test', 'ExampleController@testAuth');
});

// $router->group([], function() use ($router) {
//     $router->addRoute(['GET', 'POST'], 'yzm','UserController@test');
// });
$router->group([], function() use ($router) {
    $router->addRoute(['GET', 'POST'], 'yzm', 'YZMController@getData');
});

// test route
$router->get('/', function() {
    return 'this is a test message';
});

$router->post('/register', 'UserController@register');
$router->post('/login', 'UserController@login');
$router->post('/logout', 'UserController@logout');
$router->post('/articles/create', 'ArticleController@create');
$router->post('/image', 'ImageController@storeImage');
$router->post('/search', 'ArticleController@searchTitle');
$router->post('/searchUnderId', 'ArticleController@searchTitleUnderId');
$router->post('/articles/delete', 'ArticleController@deleteArticle');
$router->get('/articles/get', 'ArticleController@getById');
$router->get('/categories', 'CategoryController@getAll');
$router->get('/categories/getAllWithArticles', 'CategoryController@getAllWithArticles');
$router->get('/articles/getFirstImage', 'ArticleController@getFirstImage');
$router->get('/articles/getContentByPage', 'ArticleController@getContentByPage');
$router->get('/articles/getNewArticle', 'ArticleController@getNewArticle');
$router->get('/articles/getNewArticleUnderCategory', 'ArticleController@getNewArticleUnderCategory');
$router->get('/articles/newest', 'ArticleController@getNewestArticle');
$router->post('/articles/update', 'ArticleController@updateArticle');
$router->post('/articles/getUnderCategory', 'ArticleController@getUnderCategory');