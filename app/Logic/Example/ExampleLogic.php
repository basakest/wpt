<?php


namespace App\Logic\Example;

use App\Models\TestModel;
use App\Services\Example\ExampleService;
use App\Utils\Singleton;

class ExampleLogic
{
    use Singleton;

    /**
     * 获取首页数据
     * @param string $name
     * @return string
     */
    public function getHomeData(string $name):string
    {
        $result = TestModel::getInstance()->getOne(['id']);
        return "hello {$name}" . json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取配置中心数据
     * @return array|mixed
     */
    public function getNifflerConf()
    {
        return ExampleService::getInstance()->getConf();
    }

}