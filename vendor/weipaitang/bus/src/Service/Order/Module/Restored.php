<?php
namespace WptBus\Service\Order\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Order\Router;

class Restored extends BaseService
{
    /**
     * 申请订单恢复
     * @param $uri
     * @return array|void
     */
    public function apply(string $uri)
    {
        $params = ["uri" => $uri];
        if ($error = $this->validate($params, ['uri' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::APPLY_ORDER_RESTORED, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 同意订单恢复
     * @param $uri
     * @return array|void
     */
    public function agree(string $uri)
    {
        $params = ["uri" => $uri];
        if ($error = $this->validate($params, ['uri' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::AGREE_ORDER_RESTORED, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 拒绝订单恢复
     * @param $uri
     * @param $userinfoId
     * @return array|void
     */
    public function reject(string $uri)
    {
        $params = ["uri" => $uri];
        if ($error = $this->validate($params, ['uri' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::REJECT_ORDER_RESTORED, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 是否可申请恢复订单
     * @param $orderId
     * @return array|void
     */
    public function canApplyRestored(int $orderId)
    {
        $params = ["orderId" => $orderId];
        if ($error = $this->validate($params, ['orderId' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::CAN_APPLY_RESTORED, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 是否可操作恢复订单
     * @param $orderId
     * @param $userinfoId
     * @return array|void
     */
    public function canOperatorRestored(int $orderId)
    {
        $params = ["orderId" => $orderId];
        if ($error = $this->validate($params, ['orderId' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::CAN_OPERATOR_RESTORED, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 是否可同意恢复订单
     * @param $uri
     * @param $userinfoId
     * @return array|void
     */
    public function canAgreeRestored(int $orderId)
    {
        $params = ["orderId" => $orderId];
        if ($error = $this->validate($params, ['orderId' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::CAN_AGREE_RESTORED, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 获取已申请恢复订单时间
     * @param $orderId
     * @return array|void
     */
    public function getApplyRestoredTime(int $orderId)
    {
        $params = ["orderId" => $orderId];
        if ($error = $this->validate($params, ['orderId' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_APPLY_RESTORED_TIME, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 批量获取已申请订单恢复的订单
     * @param array
     * @return array|void
     */
    public function batchGetHasApplyRestored(array $orderIds)
    {
        $params = ["orderIds" => $orderIds];
        if ($error = $this->validate($params, ['orderIds' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::BATCH_GET_HAS_APPLY_RESTORED, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }
}