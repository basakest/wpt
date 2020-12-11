<?php


namespace App\Contracts\Example;

/**
 * 服务接口定义
 * Interface IExampleService
 * @package App\Contracts\Example
 */
interface IExampleService
{

    /**
     * 获取列表示例
     * @param array $fields
     * @param array $filters
     * @param array $orderBys
     * @param int $skip
     * @param int $limit
     * @return array
     */
    public function getExampleList(array $fields, array $filters, array $orderBys = [], $skip = 0, $limit = 20): array;

}