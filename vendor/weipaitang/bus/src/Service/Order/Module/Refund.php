<?php


namespace WptBus\Service\Order\Module;


use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Service\BaseService;
use WptBus\Service\Order\Router;

class Refund extends BaseService
{

    /**
     * 申请退款
     * @param string $uri
     * @param int $loginUserId
     * @param int $totalFee
     * @param int $refundFee
     * @param int $reasonId
     * @param string $reasonContent
     * @return array|void
     */
    public function applyOrderRefund(
        string $uri,
        int $loginUserId,
        int $totalFee,
        int $reasonId,
        string $reasonContent,
        string $role,
        string $action
    ) {
        $param["uri"] = $uri;
        $param["loginUserId"] = $loginUserId;
        $param["totalFee"] = $totalFee;
        $param["refundFee"] = $totalFee;
        $param["reasonId"] = $reasonId;
        $param["reasonContent"] = $reasonContent;
        $param["role"] = $role;
        $param["action"] = $action;
        if ($error = $this->validate($param, [
            'uri' => 'required',
            'totalFee' => 'required',
            'refundFee' => 'required',
            'reasonId' => 'required',
            'reasonContent' => 'required',
            'role' => 'required'
        ])) {
            return $error;
        }

        $ret = $this->httpPost(Router::APPLY_ORDER_REFUND, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 取消退款
     * @param string $uri
     * @param int $loginUserId
     * @return array|void
     */
    public function cancelOrderRefund(string $uri, int $loginUserId, string $role, $action)
    {
        $param["uri"] = $uri;
        $param["loginUserId"] = $loginUserId;
        $param["role"] = $role;
        $param["action"] = $action;
        if ($error = $this->validate($param, [
            'uri' => 'required',
            'role' => 'required'
        ])) {
            return $error;
        }

        $ret = $this->httpPost(Router::CANCEL_ORDER_REFUND, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    /**
     *  退款
     * @param int $orderId
     * @param int $loginUserId
     * @param string $operator
     * @return array|void
     */
    public function refundOrder(int $orderId, int $loginUserId, string $role, string $action)
    {
        $param["orderId"] = $orderId;
        $param["loginUserId"] = $loginUserId;
        $param["role"] = $role;
        $param["action"] = $action;
        if ($error = $this->validate($param, [
            'orderId' => 'required',
        ])) {
            return $error;
        }
        $ret = $this->httpPost(Router::REFUND_ORDER, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 查询退款
     * @param int $orderId
     * @param int $refundStatus
     * @param array $columns
     * @return array|void
     */
    public function getOrderRefund(int $orderId, string $uri, int $refundStatus, array $columns)
    {
        if (empty($orderId ?: $uri)) {
            return Response::byBus(Error::INVALID_ARGUMENT);
        }
        $param["orderId"] = $orderId;
        $param["refundStatus"] = $refundStatus;
        $param["columns"] = $columns;
        $param["uri"] = $uri;
        if ($error = $this->validate($param, [
            'refundStatus' => 'required',
            'columns' => 'required',
        ])) {
            return $error;
        }

        $ret = $this->httpPost(Router::GET_ORDER_REFUND, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;

    }

    /**
     * @param array $where
     * @param array $fields
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array|void
     */
    public function getOrderRefundList(
        array $where,
        array $fields,
        string $order = "",
        int $limit = 20,
        int $offset = 0
    ) {

        $params["fields"] = (array)$fields;
        $params["order"] = (string)$order;
        $params["limit"] = (int)$limit;
        $params["offset"] = (int)$offset;
        $params["where"] = json_encode($where, JSON_UNESCAPED_UNICODE);
        if ($error = $this->validate($params, [
            'where' => 'required',
            'fields' => 'required',
        ])) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_ORDER_REFUND_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;

    }

    /**
     * @param array $orderIds
     * @param array $columns
     * @param array $filter
     * @return array|void
     */

    public function batchGetOrderRefundById(array $orderIds, array $columns, $filter = [])
    {
        if (count($orderIds) > 200 || count($orderIds) == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT);
        }
        $filter['orderId'] = $orderIds;
        return $this->getOrderRefundList($filter, $columns, '', count($orderIds), 0);
    }

    /**
     * @param int $orderId
     * @param array $columns
     * @param int $refundStatus
     * @return array|void
     */

    public function getOrderRefundById(int $orderId, array $columns, $refundStatus = 0)
    {
        return $this->getOrderRefund($orderId, '', $refundStatus, $columns);
    }

    /**
     * @param string $uri
     * @param array $columns
     * @param int $refundStatus
     * @return array|void
     */
    public function getOrderRefundByUri(string $uri, array $columns, $refundStatus = 0)
    {
        return $this->getOrderRefund(0, $uri, $refundStatus, $columns);
    }

}