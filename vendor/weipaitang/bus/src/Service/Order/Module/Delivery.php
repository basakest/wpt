<?php


namespace WptBus\Service\Order\Module;

use WptBus\Lib\Utils;
use WptBus\Service\BaseService;
use WptBus\Service\Order\Router;

class Delivery extends BaseService
{
    /**
     * 根据订单ID获取发货地址
     * @param $orderId
     * @return array|void
     */
    public function getDeliveryAddress(int $orderId)
    {
        $params = ["orderId" => (int)$orderId, "addressType" => 0];
        if ($error = $this->validate($params, ['orderId' => 'required|integer|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_GET_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 根据订单ID获取退货地址
     * @param $orderId
     * @return array|void
     */
    public function getAfterReturnAddress(int $orderId)
    {
        $params = ["orderId" => (int)$orderId, "addressType" => 3];
        $ret = $this->httpPost(Router::DELIVERY_GET_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 根据订单uri获取发货地址
     * @param string $uri
     * @return array|void
     */
    public function getDeliveryAddressByUri(string $uri)
    {
        $params = ["addressType" => 0, "uri" => $uri];
        if ($error = $this->validate($params, ['uri' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_GET_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 获取买家订单收货货地址(鉴真阁订单查询的是第二地址，普通订单是第一地址)
     * @param int $orderId
     * @return array|void
     */
    public function getBuyerOrderAddress(int $orderId)
    {
        $params = ["orderId" => (int)$orderId];
        if ($error = $this->validate($params, ['orderId' => 'required|integer|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_GET_BUYER_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 获取买家订单收货货地址(鉴真阁订单查询的是第二地址，普通订单是第一地址)
     * @param string $uri
     * @return array|void
     */
    public function getBuyerOrderAddressByUri(string $uri)
    {
        $params = ["uri" => (string)$uri];
        if ($error = $this->validate($params, ['uri' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_GET_BUYER_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     *  根据订单ID列表批量获取发货地址
     * @param array $orderIds
     * @return array
     */
    public function batchGetDeliveryAddress(array $orderIds)
    {
        $params = ["orderId" => (array)$orderIds, "addressType" => 0];
        if ($error = $this->validate($params, ['orderId' => 'required|min:1|max:200'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_BATCH_GET_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     *  根据订单uri列表批量获取发货地址
     * @param array $uri
     * @return array
     */
    public function batchGetDeliveryAddressByUri(array $uri)
    {
        $params = ["addressType" => 0, "uri" => (array)$uri];
        if ($error = $this->validate($params, ['uri' => 'required|array|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_BATCH_GET_ADDRESS_BY_URI, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }


    /**
     * 根据订单ID获取退货地址
     * @param $orderId
     * @return array|void
     */
    public function getReturnDeliveryAddress(int $orderId)
    {
        $params = ["orderId" => (int)$orderId, "addressType" => 1];
        if ($error = $this->validate($params, ['orderId' => 'required|integer|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_GET_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 根据订单uri获取退货地址
     * @param string $uri
     * @return array|void
     */
    public function getReturnDeliveryAddressByUriOrId(string $uri)
    {
        if (mb_strlen($uri) < 16 && is_numeric($uri)) {
            $params = ["orderId" => (int)$uri, "addressType" => 1];
        } else {
            $params = ["uri" => (string)$uri, "addressType" => 1];
        }

        $ret = $this->httpPost(Router::DELIVERY_GET_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 根据订单ID列表批量获取退货地址
     * @param array $orderIds
     * @return array
     */
    public function batchGetReturnDeliveryAddress(array $orderIds)
    {

        $params = ["orderId" => (array)$orderIds, "addressType" => 1];
        if ($error = $this->validate($params, ['orderId' => 'required|min:1|max:200'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_BATCH_GET_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 根据订单uri列表批量获取退货地址
     * @param array $uri
     * @return array
     */
    public function batchGetReturnDeliveryAddressByUri(array $uri)
    {

        $params = ["addressType" => 1, "uri" => (array)$uri];
        if ($error = $this->validate($params, ['uri' => 'required|array|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_BATCH_GET_ADDRESS_BY_URI, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 根据订单ID获取发货物流
     * @param $orderId
     * @return array|void
     */
    public function getDeliveryLogistics(int $orderId)
    {
        $params = ["orderId" => (int)$orderId, "logisticsType" => 0];
        if ($error = $this->validate($params, ['orderId' => 'required|integer|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_GET_LOGISTICS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 发货-物流单号解禁
     * @param $com
     * @param $code
     * @return array|void
     */
    public function releaseLogisticsCode($com, $code)
    {
        $params = ["com" => (string)$com, "code" => (string)$code];
        if ($error = $this->validate($params, ['com' => 'required', 'code' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::RELEASE_LOGISTICS_CODE, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 根据订单ID获取第二发货物流
     * @param $orderId
     * @return array|void
     */
    public function getSecondDeliveryLogistics(int $orderId)
    {
        $params = ["orderId" => (int)$orderId, "logisticsType" => 2];
        if ($error = $this->validate($params, ['orderId' => 'required|integer|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_GET_LOGISTICS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * @param string $uri
     * @return array|void
     */
    public function getDeliveryLogisticsByUri(string $uri)
    {
        $params = ["uri" => $uri, "logisticsType" => 0];
        if ($error = $this->validate($params, ['uri' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_GET_LOGISTICS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * @param string $uri
     * @return array|void
     */
    public function getReturnDeliveryLogisticsByUri(string $uri)
    {
        $params = ["uri" => $uri, "logisticsType" => 1];
        if ($error = $this->validate($params, ['uri' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_GET_LOGISTICS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * @param string $uri
     * @return array|void
     */
    public function getAfterReturnDeliveryLogisticsByUri(string $uri)
    {
        $params = ["uri" => $uri, "logisticsType" => 3];
        if ($error = $this->validate($params, ['uri' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_GET_LOGISTICS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 根据订单ID列表批量获取发货物流
     * @param array $orderIds
     * @return array
     */
    public function batchGetDeliveryLogistics(array $orderIds)
    {
        $params = ["orderId" => (array)$orderIds, "logisticsType" => 0];
        if ($error = $this->validate($params, ['orderId' => 'required|min:1|max:200'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_BATCH_GET_LOGISTICS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 根据订单ID列表批量获取第二发货物流
     * @param array $orderIds
     * @return array
     */
    public function batchGetSecondDeliveryLogistics(array $orderIds)
    {
        $params = ["orderId" => (array)$orderIds, "logisticsType" => 2];
        if ($error = $this->validate($params, ['orderId' => 'required|min:1|max:200'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_BATCH_GET_LOGISTICS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 根据订单ID获取退货物流
     * @param $orderId
     * @return array|void
     */
    public function getReturnDeliveryLogistics(int $orderId)
    {
        $params = ["orderId" => (int)$orderId, "logisticsType" => 1];
        if ($error = $this->validate($params, ['orderId' => 'required|integer|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_GET_LOGISTICS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 根据订单ID列表批量获取退货物流
     * @param array $orderIds
     * @return array
     */
    public function batchGetReturnDeliveryLogistics(array $orderIds)
    {
        $params = ["orderId" => (array)$orderIds, "logisticsType" => 1];
        if ($error = $this->validate($params, ['orderId' => 'required|min:1|max:200'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DELIVERY_BATCH_GET_LOGISTICS, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 合买页面获取订单能一起合买的
     * @param $orderId
     * @return array|void
     */
    public function getOrderTogetherDelivery(int $orderId, $userinfoId)
    {
        $params = ["orderId" => (int)$orderId, "userinfoId" => (int)$userinfoId];
        if ($error = $this->validate($params, ['orderId' => 'required|integer|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::ORDER_TOGETHER_DEVLIERY, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 订单列表页面能可以合并发货的订单
     * @param $orderId
     * @return array|void
     */
    public function batchOrderTogetherDelivery(array $orderIds, $userinfoId)
    {
        $params = ["orderId" => (array)$orderIds, "userinfoId" => (int)$userinfoId];
        if ($error = $this->validate($params, ['orderId' => 'required|min:1|max:200'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::TOGETHER_DEVLIERY_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 获取产品库合买订单列表
     * @param $userinfoId
     * @param int $orderId
     * @return array|void
     */
    public function getDepotOrderMergeDelivery($userinfoId, int $orderId)
    {
        $params = ["orderId" => (int)$orderId, "userinfoId" => (int)$userinfoId];
        if ($error = $this->validate($params, ['orderId' => 'required|integer|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DEPOT_ORDER_MERGE_DELIVERY_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 获取订单延迟发货信息
     * @param int $orderId
     * @return array
     */
    public function getOrderDelayDeliveryInfo(int $orderId)
    {
        $params = ["orderId" => (int)$orderId];
        $ret = $this->httpPost(Router::ORDER_GET_DELAY_DEVLIERY_INFO, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 批量获取订单延迟发货信息
     * @param array $orderIds
     * @return array
     */
    public function batchOrderDelayDeliveryInfo(array $orderIds)
    {
        $params = ["orderId" => (array)$orderIds];
        if ($error = $this->validate($params, ['orderId' => 'required|min:1|max:200'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::ORDER_BATCH_GET_DELAY_DEVLIERY_INFO, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 获取延迟发货时间
     * @param $orderId
     * @return int
     */
    public function getDeliveryDelayDay($orderId)
    {
        $ret = $this->getOrderDelayDeliveryInfo((int)$orderId);
        if ($ret['code'] != 0) {
            Utils::throwException(100, '获取延迟发货信息失败');
        }
        $delay = $ret["data"];
        $day = $delay['deliveryDelayDay'] ?? 0;
        return $delay['isBuyerAgreed'] ? $day : 0;
    }

    /**
     * 批量获取延迟发货时间
     * @param $orderId
     * @return int
     */
    public function batchDeliveryDelayDays($orderId)
    {
        $delayDays = [];
        collect($orderId)->chunk(100)->each(function ($ids) use (&$delayDays) {
            $idarr = $ids->values()->toArray();
            $ret = $this->batchOrderDelayDeliveryInfo($idarr);
            if ($ret['code'] != 0) {
                Utils::throwException(100, '批量获取延迟发货信息失败');
            }
            $data = collect($ret["data"])->keyBy('orderId')->map(function ($delay) {
                $day = $delay['deliveryDelayDay'] ?? 0;
                return $delay['isBuyerAgreed'] ? $day : 0;
            })->toArray();
            $delayDays = (array)$delayDays + (array)$data;
        });
        return $delayDays;
    }

    /**
     * 是否已申请延迟发货
     * @param $orderId
     * @return bool
     */
    public function isDeliveryDelayApplied($orderId)
    {
        $ret = $this->getOrderDelayDeliveryInfo((int)$orderId);
        if ($ret['code'] != 0) {
            Utils::throwException(100, '获取延迟发货信息失败');
        }
        $delay = $ret["data"];
        return $delay['isSellerApplied'] ? true : false;
    }

    /**
     * 获取延迟发货信息
     * @param $orderId
     * @return array
     */
    public function getDeliveryDelayInfo($orderId)
    {
        $ret = $this->getOrderDelayDeliveryInfo((int)$orderId);
        if ($ret['code'] != 0) {
            Utils::throwException(100, '获取延迟发货信息失败');
        }
        $delay = $ret["data"];
        $status = $delay['deliveryDelayDealStatus'];
        return [
            'isSellerApplied' => in_array($status, [1, 3, 7]),        //是否已申请延期
            'isBuyerDealed' => in_array($status, [3, 7]),             //是否已处理延期
            'isBuyerAgreed' => $status == 7,                          //是否已同意延期
            'deliveryDelayDealAt' => $delay['deliveryDelayDealAt'],   //延期处理时间点
            'deliveryDelayDay' => $delay['deliveryDelayDay'],         //延期的天数
            'deliveryDelayReason' => $delay['deliveryDelayReason'],   //延期理由
        ];
    }

    /**
     * 申请延期发货
     * @param int $orderId
     * @param int $delayDay
     * @param string $delayReason
     * @return array
     */
    public function applyDeliveryDelay(string $uri, int $userinfoId, int $delayDay, string $delayReason = '')
    {
        $delayApplyAtDate = date("Y-m-d H:i:s");
        return $this->addOrderDelayDelivery($uri, $userinfoId, $delayDay, $delayReason, $delayApplyAtDate);
    }

    /**
     * 批量申请延期发货
     * @param array $uri
     * @param int $userinfoId
     * @param int $delayDay
     * @param string $delayReason
     * @return array
     */
    public function batchApplyDeliveryDelay(array $uri, int $userinfoId, int $delayDay, string $delayReason = '')
    {
        $delayApplyAtDate = date("Y-m-d H:i:s");
        return $this->batchAddOrderDelayDelivery($uri, $userinfoId, $delayDay, $delayReason, $delayApplyAtDate);
    }

    /**
     * 处理延期发货
     * @param int $orderId
     * @param bool $isAgree
     * @return array
     */
    public function dealDeliveryDelay(int $uri, int $userinfoId, bool $isAgree)
    {
        $status = $isAgree ? 7 : 3;
        return $this->updateOrderDelayDelivery($orderId, $status, date("Y-m-d H:i:s"));
    }


    /**
     * 添加用户发货地址
     * @param int $orderId
     * @param array $addressInfo
     * @return array
     */
    public function addOrderDeliveryAddress(int $orderId, array $addressInfo)
    {
        return $this->addOrderAddress($orderId, 0, $addressInfo);
    }

    /**
     * 添加中转地址
     * @param int $orderId
     * @param array $addressInfo
     * @return array
     */
    public function addOrderRelayAddress(int $orderId, array $addressInfo)
    {
        return $this->addOrderAddress($orderId, 2, $addressInfo);
    }

    /**
     * 添加订单退换货地址
     * @param int $orderId
     * @param array $addressInfo
     * @return array
     */
    public function addOrderReturnAddress(int $orderId, array $addressInfo)
    {
        return $this->addOrderAddress($orderId, 1, $addressInfo);
    }

    /**
     * 添加订单售后退换货地址
     * @param int $orderId
     * @param array $addressInfo
     * @return array
     */
    public function addOrderAfterReturnAddress(int $orderId, array $addressInfo)
    {
        return $this->addOrderAddress($orderId, 3, $addressInfo);
    }


    /**
     * 添加订单退换换货地址
     * @param int $orderId
     * @param int $addressType
     * @param array $addressInfo
     * @return array
     */
    private function addOrderAddress(int $orderId, int $addressType, array $addressInfo)
    {
        $params["orderId"] = (int)$orderId;
        $params["addressType"] = $addressType;
        $params["proviceFirstStageName"] = (string)$addressInfo["proviceFirstStageName"];
        $params["addressCitySecondStageName"] = (string)$addressInfo["addressCitySecondStageName"];
        $params["addressCountiesThirdStageName"] = (string)$addressInfo["addressCountiesThirdStageName"];
        $params["addressDetailInfo"] = (string)$addressInfo["addressDetailInfo"];
        $params["telNumber"] = (string)$addressInfo["telNumber"];
        $params["userName"] = (string)$addressInfo["userName"];
        $params["nationalCode"] = isset($addressInfo["nationalCode"]) ? (string)$addressInfo["nationalCode"] : "";
        $params["addressPostalCode"] = isset($addressInfo["addressPostalCode"]) ? (string)$addressInfo["addressPostalCode"] : "";
        $this->validate($params, [
            'orderId' => 'required',
            'addressType' => 'required',
            'proviceFirstStageName' => 'required',
            'addressCitySecondStageName' => 'required',
            'addressCountiesThirdStageName' => 'required',
            'addressDetailInfo' => 'required',
            'telNumber' => 'required',
            'userName' => 'required',
        ]);
        $ret = $this->httpPost(Router::ADD_ORDER_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    /**
     * 添加用户发货物流
     * @param int $orderId
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    public function saveOrderDeliveryLogistics(int $orderId, $com, $code, $expressSubscribeId = "", $imgId = "")
    {
        return $this->addOrderLogistics($orderId, 0, $com, $code, $expressSubscribeId, $imgId);
    }

    /**
     * 添加订单退换货物流
     * @param int $orderId
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    public function saveOrderReturnLogistics(int $orderId, $com, $code, $expressSubscribeId = "", $imgId = "")
    {
        return $this->addOrderLogistics($orderId, 1, $com, $code, $expressSubscribeId, $imgId);
    }

    /**
     * 添加订单售后退换货物流
     * @param int $orderId
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    public function saveAfterOrderReturnLogistics(int $orderId, $com, $code, $expressSubscribeId = "", $imgId = "")
    {
        return $this->addOrderLogistics($orderId, 3, $com, $code, $expressSubscribeId, $imgId);
    }

    /**
     * 添加订单中转物流(如先发往鉴真阁，鉴真阁发往用户(第二物流))
     * @param int $orderId
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    public function saveOrderRelayLogistics(int $orderId, $com, $code, $expressSubscribeId = "", $imgId = "")
    {
        return $this->addOrderLogistics($orderId, 2, $com, $code, $expressSubscribeId, $imgId);
    }

    /**
     * 添加订单退换换货物流
     * @param int $orderId
     * @param int $logisticsType
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    private function addOrderLogistics(int $orderId, int $logisticsType, $com, $code, $expressSubscribeId = "", $imgId = "")
    {
        $params["orderId"] = (int)$orderId;
        $params["logisticsType"] = $logisticsType;
        $params["com"] = trim((string)$com);
        $params["code"] = trim((string)$code);
        $params["imgId"] = (string)$imgId;
        $params["expressSubscribeId"] = (string)$expressSubscribeId;
        $this->validate($params, [
            'orderId' => 'required',
            'logisticsType' => 'required',
            'code' => 'required',
            'com' => 'required',
        ]);
        $ret = $this->httpPost(Router::ADD_ORDER_LOGISTICS, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 添加订单订单延时发货消息
     * @param int $orderId
     * @param $delayDay
     * @param $delayReason
     * @param $delayApplyAtDate
     * @return array
     */
    public function addOrderDelayDelivery(string $uri, $userinfoId, $delayDay, $delayReason, $delayApplyAtDate)
    {
        $params["uri"] = (string)$uri;
        $params["userinfoId"] = (int)$userinfoId;
        $params["deliveryDelayDealStatus"] = (int)1;
        $params["deliveryDelayDay"] = (int)$delayDay;
        $params["deliveryDelayReason"] = (string)$delayReason;
        $params["deliveryDelayApplyAt"] = (string)$delayApplyAtDate;
        $this->validate($params, [
            'uri' => 'required',
            'deliveryDelayDealStatus' => 'required',
            'deliveryDelayDay' => 'required',
        ]);
        $ret = $this->httpPost(Router::CHECK_AND_ADD_ORDER_DELAY_DELIVERY, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 添加订单订单延时发货消息
     * @param array $uri 订单uri列表
     * @param $userinfoId
     * @param $delayDay
     * @param $delayReason
     * @param $delayApplyAtDate
     * @return array
     */
    public function batchAddOrderDelayDelivery(array $uri, $userinfoId, $delayDay, $delayReason, $delayApplyAtDate)
    {
        $params["uris"] = (array)$uri;
        $params["userinfoId"] = (int)$userinfoId;
        $params["deliveryDelayDealStatus"] = (int)1;
        $params["deliveryDelayDay"] = (int)$delayDay;
        $params["deliveryDelayReason"] = (string)$delayReason;
        $params["deliveryDelayApplyAt"] = (string)$delayApplyAtDate;
        $this->validate($params, [
            'uris' => 'required',
            'deliveryDelayDealStatus' => 'required',
            'deliveryDelayDay' => 'required',
        ]);
        $ret = $this->httpPost(Router::BATCH_CHECK_ADD_ORDER_DELAY_DELIVERY, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 修改订单延时发货消息
     * @param int $orderId
     * @param $status
     * @param $delayDealAtDate
     * @return array
     */
    public function dealDelayDelivery(string $uri, $userinfoId, $agree)
    {
        $params["uri"] = $uri;
        $params["winId"] = (int)$userinfoId;
        $params["agree"] = (int)$agree;
        $this->validate($params, [
            'uri' => 'required',
            'winId' => 'required',
            'agree' => 'required',
        ]);
        $ret = $this->httpPost(Router::WIN_DEAL_DELAY_DELIVERY, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param array $uri 订单uri列表
     * @param $userinfoId
     * @param $agree 3买家不同意延期 、7 买家同意商家的延期申请
     * @return array
     */
    public function batchDealDelayDelivery(array $uri, $userinfoId, $agree)
    {
        $params["uris"] = $uri;
        $params["winId"] = (int)$userinfoId;
        $params["agree"] = (int)$agree;
        $this->validate($params, [
            'uris' => 'required',
            'winId' => 'required',
            'agree' => 'required',
        ]);
        $ret = $this->httpPost(Router::WIN_BATCH_DEAL_DELAY_DELIVERY, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param string $uri
     * @param $userinfiId
     * @return array
     */
    public function remindDelivery(string $uri, $userinfiId)
    {
        $param["uri"] = (string)$uri;
        $param["winId"] = (int)$userinfiId;
        $this->validate($param, [
            'uri' => 'required',
            'winId' => 'required',
        ]);
        $ret = $this->httpPost(Router::REMAIN_DELIVERY, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    public function getOrderRemindMap($orderIds)
    {
        $param["orderIds"] = $orderIds;
        $this->validate(
            $param,
            [
                'orderIds' => 'required'
            ]
        );
        $ret = $this->httpPost(Router::GET_ORDER_REMIND_MAP, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    /**
     * 修改订单物流信息
     * @param int $orderId
     * @param int $logisticsType
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    private function updateOrderLogistics(
        int $userinfoId,
        string $uri,
        int $logisticsType,
        $com,
        $code,
        $expressSubscribeId = "",
        $imgId = ""
    ) {
        $params["uri"] = $uri;
        $params["logisticsType"] = (int)$logisticsType;
        $params["code"] = trim((string)$code);
        $params["com"] = trim((string)$com);
        $params["imgId"] = (string)$imgId;
        $params["userinfoId"] = $userinfoId;
        $params["expressSubscribeId"] = (string)$expressSubscribeId;
        $this->validate($params, [
            'uri' => 'required',
            'com' => 'required',
            'code' => 'required',
        ]);
        $ret = $this->httpPost(Router::UPDATE_ORDER_LOGISTICS, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 批量修改订单物流信息
     * @param int $userinfoId
     * @param string $uri
     * @param int $logisticsType
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    private function batchUpdateOrderLogistics(
        int $userinfoId,
        array $uri,
        int $logisticsType,
        $com,
        $code,
        $expressSubscribeId = "",
        $imgId = ""
    ) {
        $params["uris"] = (array)$uri;
        $params["logisticsType"] = (int)$logisticsType;
        $params["code"] = trim((string)$code);
        $params["com"] = trim((string)$com);
        $params["imgId"] = (string)$imgId;
        $params["userinfoId"] = $userinfoId;
        $params["expressSubscribeId"] = (string)$expressSubscribeId;
        $this->validate($params, [
            'uris' => 'required',
            'com' => 'required',
            'code' => 'required',
        ]);
        $ret = $this->httpPost(Router::BATCH_UPDATE_ORDER_LOGISTICS, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 批量修改订单发货物流
     * @param $userinfoId
     * @param array $uri
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    public function batchUpdateOrderDeliveryLogistics($userinfoId, array $uri, $com, $code, $expressSubscribeId = "", $imgId = "")
    {
        return $this->batchUpdateOrderLogistics($userinfoId, $uri, 0, $com, $code, $expressSubscribeId, $imgId);
    }

    /**
     * 修改订单发货物流
     * @param int $orderId
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    public function updateOrderDeliveryLogistics($userinfoId, $uri, $com, $code, $expressSubscribeId = "", $imgId = "")
    {
        return $this->updateOrderLogistics($userinfoId, $uri, 0, $com, $code, $expressSubscribeId, $imgId);
    }

    /**
     * 修改订单退货物流
     * @param int $orderId
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    public function updateOrderReturnLogistics($userinfoId, $uri, $com, $code, $expressSubscribeId = "", $imgId = "")
    {
        return $this->updateOrderLogistics($userinfoId, $uri, 1, $com, $code, $expressSubscribeId, $imgId);
    }

    /**
     * 批量修改订单退货物流
     * @param $userinfoId
     * @param $uri
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    public function batchUpdateOrderReturnLogistics($userinfoId, array $uri, $com, $code, $expressSubscribeId = "", $imgId = "")
    {
        return $this->batchUpdateOrderLogistics($userinfoId, $uri, 1, $com, $code, $expressSubscribeId, $imgId);
    }

    /**
     * 修改售后订单退货物流
     * @param int $orderId
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    public function updateOrderAfterReturnLogistics($userinfoId, $uri, $com, $code, $expressSubscribeId = "", $imgId = "")
    {
        return $this->updateOrderLogistics($userinfoId, $uri, 3, $com, $code, $expressSubscribeId, $imgId);
    }

    /**
     * 批量修改售后订单退货物流
     * @param $userinfoId
     * @param $uri
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    public function batchUpdateOrderAfterReturnLogistics($userinfoId, array $uri, $com, $code, $expressSubscribeId = "", $imgId = "")
    {
        return $this->batchUpdateOrderLogistics($userinfoId, $uri, 3, $com, $code, $expressSubscribeId, $imgId);
    }

    /**
     * 重置订单第一收货地址
     * (如：取消鉴定服务，删除鉴真阁第一收货地址，将用户地址改为第一收货地址)
     * @param string $uri
     * @return array
     */
    public function resetOrderFirstDeliveryAddressByUriOrId(string $uri)
    {
        $params["uri"] = (string)$uri;
        $ret = $this->httpPost(Router::REST_ORDER_FIRST_DELIVERY_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 0);
            return $data;
        });
        return $ret;
    }

    /**
     * 发货
     * @param int $orderId
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    public function toDelivery(int $userinfoId, string $uri, $com, $code, $expressSubscribeId = "", $imgId = "")
    {
        $params["uri"] = (string)$uri;
        $params["userinfoId"] = (int)$userinfoId;
        $params["code"] = (string)$code;
        $params["com"] = (string)$com;
        $params["imgId"] = (string)$imgId;
        $params["expressSubscribeId"] = (string)$expressSubscribeId;
        $this->validate($params, [
            'uri' => 'required',
            'userinfoId' => 'required',
            'com' => 'required',
            'code' => 'required',
        ]);
        $ret = $this->httpPost(Router::TO_DELIVERY, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 0);
            $data->data->win = json_decode($data->data->winJson);
            $data->data->profile = json_decode($data->data->profileJson);
            $data->data->profile->content = $data->data->content;
            return $data;
        });
        return $ret;
    }

    /**
     * 发货-无收货地址
     * @param int $orderId
     * @return array
     */
    public function toDeliveryWithoutAddress(int $userinfoId, string $uri)
    {
        $params["uri"] = (string)$uri;
        $params["userinfoId"] = (int)$userinfoId;
        $this->validate($params, [
            'uri' => 'required',
            'userinfoId' => 'required',
        ]);
        $ret = $this->httpPost(Router::TO_DELIVERY_WITHOUT_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 0);
            $data->data->win = json_decode($data->data->winJson);
            $data->data->profile = json_decode($data->data->profileJson);
            $data->data->profile->content = $data->data->content;
            return $data;
        });
        return $ret;
    }

    /**
     * 发货
     * @param int $orderId
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    public function multiToDeliveryCheck(int $userinfoId, array $uri, string $com, string $code, string $imgId)
    {
        $params["uri"] = (array)$uri;
        $params["userinfoId"] = (int)$userinfoId;
        $params["com"] = trim((string)$com);
        $params["code"] = trim((string)$code);
        $params["imgId"] = trim((string)$imgId);
        $this->validate($params, [
            'uri' => 'required',
            'userinfoId' => 'required',
            'com' => 'required',
            'code' => 'required',
        ]);
        $ret = $this->httpPost(Router::MULTI_TO_DELIVERY_CHECK, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 检测物流单号是否重复使用了
     * @param int $orderId
     * @param int $logisticsType
     * @param $com
     * @param $code
     * @param string $expressSubscribeId
     * @param string $imgId
     * @return array
     */
    public function checkLogisticsCode(int $userinfoId, $uris, $com, $code)
    {
        if (is_string($uris)) {
            $uris = [$uris];
        }
        $params["userinfoId"] = (int)$userinfoId;
        $params["uris"] = (array)$uris;
        $params["code"] = (string)$code;
        $params["com"] = (string)$com;
        $this->validate($params, [
            'userinfoId' => 'required',
            'uris' => 'required|array',
            'com' => 'required',
            'code' => 'required',
        ]);
        $ret = $this->httpPost(Router::CHECK_LOGISTICS_CODE, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 退货发货物流单号校验
     * @param int $userinfoId
     * @param $uris
     * @param $com
     * @param $code
     * @return array
     */
    public function orderReturnLogisticsCheck(int $userinfoId, $uris, $com, $code)
    {
        if (is_string($uris)) {
            $uris = [$uris];
        }
        $params["userinfoId"] = (int)$userinfoId;
        $params["uris"] = (array)$uris;
        $params["code"] = (string)$code;
        $params["com"] = (string)$com;
        $this->validate($params, [
            'userinfoId' => 'required',
            'uris' => 'required|array',
            'com' => 'required',
            'code' => 'required',
        ]);
        $ret = $this->httpPost(Router::ORDER_RETURN_LOGISTICS_CHECK, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    /**
     * 发货信息[拍品]
     * @param string $saleUri
     * @param int $addressIndex
     * @param int $userinfoId
     * @return array
     */
    public function getOrderAuctionDeliveryInfo(string $saleUri, int $userinfoId, int $addressIndex = null)
    {
        $params["saleUri"] = (string)$saleUri;
        $params["addressIndex"] = (int)$addressIndex;
        $params["userId"] = (int)$userinfoId;

        $this->validate($params, [
            'saleUri' => 'required',
            'userId' => 'required',
        ]);
        $ret = $this->httpPost(Router::GET_ORDER_AUCTION_DELIVERY_INFO, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, true);
            return $data;
        });
        return $ret;
    }

    /**
     * 发货信息[产品库]
     * @param string $saleUri
     * @param int $addressIndex
     * @param int $userinfoId
     * @return array
     */
    public function getOrderDepotDeliveryInfo($saleUri, $userinfoId, $addressIndex = null)
    {
        $params["saleUri"] = (string)$saleUri;
        $params["addressIndex"] = (int)$addressIndex;
        $params["userId"] = (int)$userinfoId;

        $this->validate($params, [
            'saleUri' => 'required',
            'userId' => 'required',
            '$addressIndex' => 'required',
        ]);
        $ret = $this->httpPost(Router::GET_ORDER_DEPOT_DELIVERY_INFO, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, true);
            return $data;
        });
        return $ret;
    }

    /**
     * 绑定地址到订单上[添加]
     * @param string $saleUri
     * @param int $addressIndex
     * @param int $userinfoId
     * @return array
     */
    public function bindOrderAddress(string $saleUri, int $userinfoId, int $addressIndex = null)
    {
        $params["saleUri"] = (string)$saleUri;
        $params["addressIndex"] = (int)$addressIndex;
        $params["userId"] = (int)$userinfoId;

        $this->validate($params, [
            'saleUri' => 'required',
            'userId' => 'required',
            'addressIndex' => 'required',
        ]);
        $ret = $this->httpPost(Router::BIND_ORDER_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, true);
            return $data;
        });
        return $ret;
    }

    /**
     * 绑定地址到订单上[修改]
     * @param string $saleUri
     * @param int $addressIndex
     * @param int $userinfoId
     * @return array
     */
    public function bindOrderAddressByInfo(int $orderId, int $addressType, array $addressInfo)
    {
        $params["orderId"] = (int)$orderId;
        $params["addressType"] = $addressType;
        $params["proviceFirstStageName"] = (string)$addressInfo["proviceFirstStageName"];
        $params["addressCitySecondStageName"] = (string)$addressInfo["addressCitySecondStageName"];
        $params["addressCountiesThirdStageName"] = (string)$addressInfo["addressCountiesThirdStageName"];
        $params["addressDetailInfo"] = (string)$addressInfo["addressDetailInfo"];
        $params["telNumber"] = (string)$addressInfo["telNumber"];
        $params["userName"] = (string)$addressInfo["userName"];
        $params["nationalCode"] = isset($addressInfo["nationalCode"]) ? (string)$addressInfo["nationalCode"] : "";
        $params["addressPostalCode"] = isset($addressInfo["addressPostalCode"]) ? (string)$addressInfo["addressPostalCode"] : "";
        $this->validate($params, [
            'orderId' => 'required',
            'addressType' => 'required',
            'proviceFirstStageName' => 'required',
            'addressCitySecondStageName' => 'required',
            'addressCountiesThirdStageName' => 'required',
            'addressDetailInfo' => 'required',
            'telNumber' => 'required',
            'userName' => 'required',
        ]);
        $ret = $this->httpPost(Router::BIND_ORDER_ADDRESS_BY_INFO, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, true);
            return $data;
        });
        return $ret;
    }


    /**
     * 卖家主动提醒买家绑定地址
     * @param string $saleUri
     * @param int $userinfoId
     * @return array
     */
    public function remindBuyerBindAddress(string $saleUri, int $userinfoId)
    {
        $params["saleUri"] = (string)$saleUri;
        $params["userinfoId"] = $userinfoId;
        $this->validate($params, [
            'saleUri' => 'required',
            'userinfoId' => 'required',
        ]);
        $ret = $this->httpPost(Router::REMIND_BUYER_BIND_ADDRESS, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, true);
            return $data;
        });
        return $ret;
    }


    /**
     * 提醒绑定地址按钮
     * @param int $saleUri
     * @param int $userinfoId
     * @return array
     */
    public function getRemindBindAdd(int $saleId, int $userinfoId)
    {
        $params["saleId"] = $saleId;
        $params["userinfoId"] = $userinfoId;
        $this->validate($params, [
            'saleId' => 'required',
            'userinfoId' => 'required',
        ]);
        $ret = $this->httpPost(Router::GET_REMIND_BIND_ADD, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, true);
            return $data;
        });
        return $ret;
    }
}
