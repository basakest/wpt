<?php


namespace WptOrder\OrderService\Contracts;


interface OrderApi
{

    /**
     * @param int $id
     * @param array $fields
     * @return mixed
     */
    public function getOrderById(int $id, array $fields);

    /**
     * @param string $uri
     * @param array $fields
     * @return mixed
     */
    public function getOrderByUri(string $uri, array $fields);

    /**
     * @param array $condition
     * @param array $fields
     * @return mixed
     */
    public function getOrderByCondition(array $condition, array $fields);

    /**
     * @param array $condition
     * @param array $fields
     * @return array
     */
    public function getOrdersByCondition(array $condition, array $fields): array;


    /**
     * @param string $pid
     * @return array|mixed
     */
    public function getSaleIdByPid($pid);

}