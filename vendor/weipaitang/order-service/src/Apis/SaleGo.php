<?php


namespace WptOrder\OrderService\Apis;


use App\Facades\Sale\Sale;
use WptCommon\Library\Tools\Logger;
use WptOrder\OrderService\Contracts\OrderApi;
use WptOrder\OrderService\Exceptions\RequestFailException;
use WptOrder\OrderService\OrderService;
use WptCommon\Library\Facades\MLogger;

class SaleGo implements OrderApi
{
    /**
     * @var OrderService
     */
    protected $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }

    /**
     * 根据 id 获取订单信息
     * @param int $id 订单id
     * @param array $fields 查询字段
     * @return mixed|null
     * @throws RequestFailException
     */
    public function getOrderById(int $id, array $fields)
    {
        return $this->getOrderByUri((string)$id, $fields);
    }

    /**
     * 根据拍品 uri 获取订单信息
     * @param string $uri 拍品uri
     * @param array $fields 查询字段
     * @return mixed|null
     */
    public function getOrderByUri(string $uri, array $fields)
    {
        Logger::getInstance()->info("order-service", "请求微服务订单数据", ['arguments' => compact("uri", 'fields')]);

        $response = Sale::getOrder($uri, $fields);

        Logger::getInstance()->info("order-service", "请求微服务订单返回", ['arguments' => json_encode($response)]);

        if ($response->success === false) {
            throw new RequestFailException("微服务调用失败, message: {$response->message}. method: " . __METHOD__ . ", arguments: " . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));
        }

        if (empty($response->data)) {
            return null;
        }

        return chang_array_to_object($response->data);
    }

    /**
     * 按条件查询订单信息
     * @param array $condition
     * @param array $fields
     * @return mixed|null
     */
    public function getOrderByCondition(array $condition, array $fields)
    {
        $orders = $this->getOrdersByCondition($condition, $fields, 1);
        return !empty($orders) ? reset($orders) : null;
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
        $response = Sale::getOrderList($fields, $condition, $order, $limit, $offset, $index);
        if ($response->success === false) {
            throw new RequestFailException("微服务调用失败, message: {$response->message}. method: " . __METHOD__ . ", arguments: " . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));
        }

        if (empty($response->data)) {
            return [];
        }

        return chang_array_to_object($response->data);
    }

    /**
     * @param string $pid
     * @return array|mixed
     */
    public function getSaleIdByPid($pid)
    {
        if (!$pid) {
            return [];
        }
        $response = Sale::getSaleIdByPid($pid);

        if ($response->success === false) {

            MLogger::error("order-service-2", "通过Pid获取saleId微服务调用失败", [$pid, $response]);
            return [];
            //throw new RequestFailException("微服务调用失败, message: {$response->message}. method: " . __METHOD__ . ", arguments: " . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));
        }
        if (empty($response->data)) {
            return [];
        }

        return chang_array_to_object($response->data);
    }


}