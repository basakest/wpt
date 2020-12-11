<?php


namespace WptOrder\OrderService\Apis;


use WptOrder\OrderService\Contracts\OrderApi;

/**
 * 空的接口，sdk 测试时用
 * Class OrderGo
 * @package WptOrder\OrderService\Apis
 */
class OrderGo implements OrderApi
{
    /**
     * 根据 id 获取订单信息
     * @param int $id 订单id
     * @param array $fields 查询字段
     * @return mixed|null
     */
    public function getOrderById(int $id, array $fields)
    {
        return json_decode(json_encode(['saleId' => 1]));
    }

    /**
     * 根据拍品 uri 获取订单信息
     * @param string $uri 拍品uri
     * @param array $fields 查询字段
     * @return mixed|null
     */
    public function getOrderByUri(string $uri, array $fields)
    {
        return json_decode(json_encode(['saleId' => 1]));
    }

    /**
     * 按条件查询订单信息
     * @param array $condition
     * @param array $fields
     * @return mixed|null
     */
    public function getOrderByCondition(array $condition, array $fields)
    {
        return [
            json_encode(['saleId' => 1])
        ];
    }

    /**
     * 按条件获取订单列表
     * @param array $condition
     * @param array $fields
     * @param int|null $limit
     * @param int|null $offset
     * @param string $order
     * @param string $index
     * @return array
     */
    public function getOrdersByCondition(array $condition, array $fields, int $limit = null, int $offset = null, string $order = '', string $index = ''): array
    {
        return [
            json_encode(['saleId' => 1])
        ];
    }
}