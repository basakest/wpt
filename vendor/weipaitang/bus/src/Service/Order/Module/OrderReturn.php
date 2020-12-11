<?php

namespace WptBus\Service\Order\Module;

use App\ConstDir\ErrorConst;
use App\Utils\CommonUtil;
use Monolog\Logger;
use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Lib\Validator;
use WptBus\Service\Order\Consts\OrderReturnConst;
use WptBus\Service\Order\Router;
use WptCommon\Library\Facades\MLogger;

class OrderReturn extends \WptBus\Service\BaseService
{


    /**
     * 申请退货
     * @param string $uri
     * @param int $userinfoId
     * @param int $reasonId
     * @param string $reason
     * @param int $returnFee
     * @param int $totalFee
     * @param string $action
     * @param string $content
     * @param int $postFee
     * @param int $returnType
     * @param int $receiveStatus
     * @param array $proveImages
     * @return array
     */
    public function applyReturn(
        string $uri,
        int $userinfoId,
        int $reasonId,
        string $reason,
        int $returnFee,
        int $totalFee,
        string $action,
        string $content = "",
        int $postFee = 0,
        int $returnType = 1,
        int $receiveStatus = 1,
        array $proveImages = []
    ) {
        $content = trim($content);
        $params["uri"] = $uri;
        $params["userinfoId"] = (int)$userinfoId;
        $params["reasonId"] = (int)$reasonId;
        $params["reason"] = trim((string)$reason);
        $params["content"] = $content;
        $params["refundFee"] = (int)$returnFee;
        $params["totalFee"] = (int)$totalFee;
        $params["action"] = (string)$action;
        $params["returnType"] = $returnType;
        $params["receiveStatus"] = $receiveStatus;
        $params["reasonContent"] = $content;
        $params["proveImages"] = $proveImages;
        $this->validate($params, [
            'uri' => 'required',
            'userinfoId' => 'required',
            'reasonId' => 'required',
            'returnFee' => 'required',
            'totalFee' => 'required',
        ]);
        $ret = $this->httpPost(Router::APPLY_ORDER_RETURN, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 同意退货
     * @param string $uri
     * @param int $userinfoId
     * @param array $addressInfo
     * @param string $action
     * @param string $role
     * @return array
     */
    public function agreeReturn(string $uri, int $userinfoId, array $addressInfo, string $action, string $role = "seller")
    {
        $params["uri"] = $uri;
        $params["userinfoId"] = (int)$userinfoId;
        $params["role"] = (string)$role;
        $params["proviceFirstStageName"] = (string)$addressInfo["proviceFirstStageName"];
        $params["addressCitySecondStageName"] = (string)$addressInfo["addressCitySecondStageName"];
        $params["addressCountiesThirdStageName"] = (string)$addressInfo["addressCountiesThirdStageName"];
        $params["addressDetailInfo"] = (string)$addressInfo["addressDetailInfo"];
        $params["telNumber"] = (string)$addressInfo["telNumber"];
        $params["userName"] = (string)$addressInfo["userName"];
        $params["nationalCode"] = isset($addressInfo["nationalCode"]) ? (string)$addressInfo["nationalCode"] : "";
        $params["addressPostalCode"] = isset($addressInfo["addressPostalCode"]) ? (string)$addressInfo["addressPostalCode"] : "";
        $params["action"] = (string)$action;
        $this->validate($params, [
            'uri' => 'required',
            'userinfoId' => 'required',
            'proviceFirstStageName' => 'required',
            'addressCitySecondStageName' => 'required',
            'addressCountiesThirdStageName' => 'required',
            'addressDetailInfo' => 'required',
            'telNumber' => 'required',
            'userName' => 'required',
        ]);
        $ret = $this->httpPost(Router::AGREE_ORDER_RETURN, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 拒绝退货
     * @param string $uri
     * @param int $reasonId
     * @param string $reason
     * @param int $userinfoId
     * @param string $action
     * @param string $content
     * @return array
     */
    public function rejectReturn(string $uri, int $userinfoId, int $reasonId, string $reason, string $action, string $content = "")
    {
        $params["uri"] = $uri;
        $params["userinfoId"] = (int)$userinfoId;
        $params["reasonId"] = (int)$reasonId;
        $params["reason"] = trim((string)$reason);
        $params["content"] = $content;
        $params["action"] = $action;
        $this->validate($params, [
            'uri' => 'required',
            'userinfoId' => 'required',
        ]);
        $ret = $this->httpPost(Router::REJECT_ORDER_RETURN, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 获取进行中的退货退款记录
     * isActive = 1
     * @param string $uriOrId
     * @return array
     */
    public function getReturnOrder($uriOrId)
    {
        $params["uri"] = (string)$uriOrId;
        $this->validate($params, [
            'uri' => 'required',
        ]);
        $ret = $this->httpPost(Router::GET_RETURN_ORDER, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 获取订单退货单最近的一条记录包括关闭的
     * @param int $orderId
     * @param array $fields
     * @param array $filter
     * @return array
     */
    public function getOrderReturnById(int $orderId, array $fields, $filter = [])
    {
        if ($orderId == 0 || count($fields) == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT);
        }
        if (!in_array('orderId', $fields)) {
            $fields[] = 'orderId';
        }
        foreach ($fields as $v) {
            if (!in_array($v, OrderReturnConst::OrderReturnFields)) {
                return Response::byBus(Error::INVALID_ARGUMENT);
            }
        }
        $filter['orderId'] = $orderId;
        $params['where'] = json_encode($filter, JSON_UNESCAPED_UNICODE);
        $params['limit'] = 1;
        $params['offset'] = 0;
        $params['orderBy'] = "createTime desc";
        $params['fields'] = $fields;
        $ret = $this->httpPost(Router::GET_ORDER_RETURN_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            if (count($data) == 0) {
                return [];
            }
            $returnData = collect($data)->keyBy('orderId')->toArray();
            return $returnData;
        });
        return $ret;

    }

    /**
     * 单次不能超过10个
     * 批量获取订单退货单最近的一条记录包括关闭的
     * @param array $orderIds
     * @param array $fields
     * @param array $filter
     * @return array
     */
    public function batchGetOrderReturnById(array $orderIds, array $fields, $filter = [])
    {
        if (count($orderIds) == 0 || count($orderIds) > 20 || count($fields) == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT);
        }
        if (!in_array("createTime", $filter)) {
            array_push($fields, 'createTime');
        }
        $filter['orderId'] = $orderIds;
        $params['where'] = json_encode($filter, JSON_UNESCAPED_UNICODE);
        $params['limit'] = 300;
        $params['offset'] = 0;
        $params['orderBy'] = "createTime desc";
        $params['fields'] = array_values($fields);
        $ret = $this->httpPost(Router::GET_ORDER_RETURN_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            if (count($data) == 0) {
                return [];
            }
            $returnData = collect($data)->groupBy('orderId')->map(function ($item) {
                return collect($item)->sortByDesc('createTime')->first();
            })->toArray();
            return $returnData;
        });
        return $ret;

    }

    /**
     * 获取售中或者售后退货记录
     * @param $orderId
     * @param int $isMoneyReturn
     * @return array
     */
    public function getOrderReturnInfo($orderId, $isMoneyReturn = 0)
    {
        if ($orderId == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT);
        }

        $where = ['orderId' => $orderId, 'isMoneyReturn' => $isMoneyReturn];
        $params['where'] = json_encode($where, JSON_UNESCAPED_UNICODE);
        $params['limit'] = 1;
        $params['offset'] = 0;
        $params['orderBy'] = "createTime desc";
        $params['fields'] = [
            'id',
            'orderId',
            'userinfoId',
            'returnToUserId',
            'returnToAddress',
            'returnToDelivery',
            'returnDeliveryTime',
            'createTime',
            'reasonId',
            'reason',
            'returnStatus',
            'expectRefundFee',
            'remark',
            'returnType',
            'receiveStatus',
            'refundFee',
            'totalFee'
        ];
        $ret = $this->httpPost(Router::GET_ORDER_RETURN_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            if (count($data) == 0) {
                return [];
            }
            $returnData = $data[0];
            return $returnData;
        });
        return $ret;
    }

    /**
     * 根据orderId批量获取售中退货单
     * @param array $orderIds
     * @param int $isMoneyReturn
     * @param int $isActive
     * @param array $fields 可选字段 ['id','orderId','userinfoId','returnToUserId','returnToAddress','returnToDelivery','returnDeliveryTime',
     * 'createTime','reasonId','reason','returnStatus','expectRefundFee','remark','returnType','receiveStatus','refundFee','totalFee']
     * @return array
     */
    public function getOrderReturnListById(array $orderIds, array $fields, $isMoneyReturn = 0, $isActive = 1)
    {
        if (count($orderIds) == 0 || count($fields) == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT);
        }

        if (!in_array('orderId', $fields)) {
            $fields[] = 'orderId';
        }
        foreach ($fields as $v) {
            if (!in_array($v, OrderReturnConst::OrderReturnFields)) {
                return Response::byBus(Error::INVALID_ARGUMENT);
            }
        }

        $where = ['orderId' => $orderIds, 'isMoneyReturn' => $isMoneyReturn, 'isActive' => $isActive];
        $params['where'] = json_encode($where, JSON_UNESCAPED_UNICODE);
        $params['limit'] = count($orderIds);
        $params['offset'] = 0;
        $params['orderBy'] = "createTime desc";
        $params['fields'] = $fields;

        $ret = $this->httpPost(Router::GET_ORDER_RETURN_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            if (count($data) == 0) {
                return [];
            }
            $returnData = collect($data)->keyBy('orderId')->toArray();
            return $returnData;
        });
        return $ret;

    }

    /**
     * 买家退货发货
     * @param string $uri
     * @param int $userinfoId
     * @param string $com
     * @param string $code
     * @param string $otherDelivery
     * @param string $action
     * @param int $isMoneyReturn
     * @return array
     */
    public function toDeliveryReturn(string $uri, int $userinfoId, string $com, string $code, string $otherDelivery, string $action)
    {
        $params["uri"] = $uri;
        $params["userinfoId"] = $userinfoId;
        $params["com"] = $com;
        $params["code"] = $code;
        $params["otherDelivery"] = $otherDelivery;
        $params["action"] = $action;
        $this->validate($params, [
            'uri' => 'required',
            "userinfoId" => "required",
        ]);
        $ret = $this->httpPost(Router::TO_DELIVERY_RETURN, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    /**
     * @param string $uri
     * @param int $userinfoId
     * @param string $role
     * @param string $action
     * @return array
     */
    public function agreeReturnRefund(string $uri, int $userinfoId, string $role, string $action)
    {
        $param["uri"] = $uri;
        $param["userinfoId"] = $userinfoId;
        $param["role"] = $role;
        $param["action"] = $action;
        if ($error = $this->validate($param, [
            'uri' => 'required',
            'role' => 'required'
        ])) {
            return $error;
        }
        $ret = $this->httpPost(Router::AGREE_RETURN_REFUND, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    /**
     * @param string $uri
     * @param int $reasonId
     * @param string $reason
     * @param int $totalFee
     * @param int $refundFee
     * @param string $actionName
     * @param int $isMoneyReturn
     * @param string $opName
     * @param int $expectRefundFee
     * @param int $postFee
     * @param string $paymentRecords
     * @return array|void
     */
    public function dashboardAgreeReturnRefund(
        string $uri,
        int $reasonId,
        string $reason,
        int $totalFee,
        int $refundFee,
        string $actionName = '',
        int $isMoneyReturn = 0,
        string $opName = '',
        int $expectRefundFee = 0,
        int $postFee = 0,
        string $paymentRecords = ''
    ) {
        $param["uri"] = $uri;
        $param["reasonId"] = (int)$reasonId;
        $param["reason"] = $reason;
        $param["totalFee"] = (int)$totalFee;
        $param["refundFee"] = (int)$refundFee;
        $param["action"] = $actionName;
        $param["isMoneyReturn"] = (int)$isMoneyReturn;
        $param["opName"] = $opName;
        $param["expectRefundFee"] = (int)$expectRefundFee;
        $param["postFee"] = $postFee;
        $param["paymentRecords"] = $paymentRecords;
        if ($error = $this->validate($param, [
            'uri' => 'required',
            'isMoneyReturn' => 'required',
            'action' => 'required',
        ])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DASHBOARD_RETURN_REFUND, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 后台同意退货
     * @param string $uri
     * @param int $userinfoId
     * @param array $addressInfo
     * @param int $refundFee
     * @param int $totalFee
     * @param string $action
     * @param int $isMoneyReturn
     * @param int $reasonId
     * @param string $reason
     * @param string $opName
     * @param int $expectRefundFee
     * @param $returnToUid
     * @param $postFee
     * @param $paymentRecords
     * @return array
     */
    public function dashboardAgreeReturn(
        string $uri,
        int $userinfoId,
        array $addressInfo,
        int $refundFee,
        int $totalFee,
        string $action,
        int $isMoneyReturn,
        int $reasonId,
        string $reason,
        string $opName,
        $expectRefundFee,
        $returnToUid,
        int $postFee = 0,
        string $paymentRecords = ''
    ) {
        $params["uri"] = $uri;
        $params["userinfoId"] = (int)$userinfoId;
        $params["refundFee"] = (int)$refundFee;
        $params["totalFee"] = (int)$totalFee;
        $params["proviceFirstStageName"] = (string)$addressInfo["proviceFirstStageName"];
        $params["addressCitySecondStageName"] = (string)$addressInfo["addressCitySecondStageName"];
        $params["addressCountiesThirdStageName"] = (string)$addressInfo["addressCountiesThirdStageName"];
        $params["addressDetailInfo"] = (string)$addressInfo["addressDetailInfo"];
        $params["telNumber"] = (string)$addressInfo["telNumber"];
        $params["userName"] = (string)$addressInfo["userName"];
        $params["nationalCode"] = isset($addressInfo["nationalCode"]) ? (string)$addressInfo["nationalCode"] : "";
        $params["addressPostalCode"] = isset($addressInfo["addressPostalCode"]) ? (string)$addressInfo["addressPostalCode"] : "";
        $params["action"] = $action;
        $params["isMoneyReturn"] = $isMoneyReturn;
        $params["opName"] = $opName;
        $params["reasonId"] = (int)$reasonId;
        $params["reason"] = $reason;
        $params["expectRefundFee"] = (int)$expectRefundFee;
        $params["returnToUid"] = (int)$returnToUid;
        $params["postFee"] = $postFee;
        $params["paymentRecords"] = $paymentRecords;
        if ($error = $this->validate($params, [
            'uri' => 'required',
            'proviceFirstStageName' => 'required',
            'addressCitySecondStageName' => 'required',
            'addressCountiesThirdStageName' => 'required',
            'addressDetailInfo' => 'required',
            'telNumber' => 'required',
            'userName' => 'required',
        ])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DASHBOARD_AGREE_ORDER_RETURN, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 后台拒绝退货
     * @param string $uri
     * @param int $userinfoId
     * @param int $reasonId
     * @param string $reason
     * @param int $refundFee
     * @param int $totalFee
     * @param int $isMoneyReturn
     * @param string $opName
     * @param int $expectRefundFee
     * @param string $action
     * @return array
     */
    public function dashboardRejectReturn(
        string $uri,
        int $userinfoId,
        int $reasonId,
        string $reason,
        int $refundFee,
        int $totalFee,
        int $isMoneyReturn,
        string $opName = '',
        int $expectRefundFee = 0,
        string $action = ''
    ) {
        $params["uri"] = $uri;
        $params["userinfoId"] = (int)$userinfoId;
        $params["reasonId"] = (int)$reasonId;
        $params["reason"] = trim((string)$reason);
        $params["refundFee"] = (int)$refundFee;
        $params["totalFee"] = (int)$totalFee;
        $params["isMoneyReturn"] = (int)$isMoneyReturn;
        $params["opName"] = $opName;
        $params["expectRefundFee"] = (int)$expectRefundFee;
        $params["action"] = $action;
        if ($error = $this->validate($params, [
            'uri' => 'required',
            'userinfoId' => 'required',
        ])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DASHBOARD_REJECT_ORDER_RETURN, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 撤销工单
     * @param string $uriOrId
     * @param string $action
     * @return array
     */
    public function cancelOrderReturn(string $uriOrId, string $action, string $role, string $reason)
    {
        $param["uriOrId"] = $uriOrId;
        $param["action"] = $action;
        $param["role"] = $role;
        $param["reason"] = $reason;
        if ($error = $this->validate($param, [
            'uriOrId' => 'required',
            'action' => 'required'
        ])) {
            return $error;
        }
        $ret = $this->httpPost(Router::CANCEL_RETURN, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 创建退货单
     * @param array $ary
     *
     * @return array $ary
     */
    public function createOrderReturn(array $returnInfo)
    {
        if ($error = $this->validate($returnInfo, [
            'orderId' => 'required',
        ])) {
            return $error;
        }
        $param["data"] = $returnInfo;
        $ret = $this->httpPost(Router::CREATE_ORDER_RETURN, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    public function updateOrderReturn(int $returnId, array $returnInfo)
    {
        $param["returnId"] = $returnId;
        $param["data"] = $returnInfo;
        if ($error = $this->validate($param, [
            'returnId' => 'required',
            'data' => 'required',
        ])) {
            return $error;
        }
        $ret = $this->httpPost(Router::UPDATE_ORDER_RETURN, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 创建退款单
     * @param array $ary
     *
     * @return array $ary
     */
    public function createOrderRefund(array $ary)
    {
        $url = "order/order-refund/create-order-refund";
        $param["data"] = $ary;
        $ret = $this->httpPost($url, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    /**
     * 创建操作记录
     * @param array $ary
     *
     *
     * @return array $ary
     */
    public function createOrderRecord(array $ary)
    {
        $url = "order/order-return/create-order-handle-record";
        $param["data"] = $ary;
        $ret = $this->httpPost($url, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 更新退款单
     * @param int $refundId
     * @param array $ary
     *
     * @return array $ret
     */
    public function updateOrderRefund(int $refundId, array $ary)
    {
        $url = "order/order-refund/update-order-refund";
        $param['refundId'] = $refundId;
        $param["data"] = $ary;
        $ret = $this->httpPost($url, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    /**
     * 获取已插入退货单id
     * @param int $orderId
     * @param array $ary
     *
     * @return array $ret
     */
    public function getOrderReturnIds(int $orderId)
    {
        $url = "order/order-return/tmp-get-return-orders";
        $param['orderId'] = $orderId;
        $ret = $this->httpPost($url, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        if (empty($ret['data'])) {
            return [];
        }
        return $ret['data'];
    }

    /**
     * 获取已插入退款单id
     * @param int $orderId
     * @param array $ary
     *
     * @return array $ret
     */
    public function getOrderRefundIds(int $orderId)
    {
        $url = "order/order-refund/tmp-get-order-refunds";
        $param['orderId'] = $orderId;
        $ret = $this->httpPost($url, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        if (empty($ret['data'])) {
            return [];
        }
        return $ret['data'];
    }

    /**
     * 获取已插入记录id
     * @param int $resultId
     * @param int $orderId
     * @param array $ary
     *
     * @return array $ret
     */
    public function getOrderRecordIds(int $resultId, int $orderId)
    {
        $url = "order/order-return/tmp-get-order-handle-records";
        $param['resultId'] = $resultId;
        $param['orderId'] = $orderId;
        $ret = $this->httpPost($url, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        if (empty($ret['data'])) {
            return [];
        }
        return $ret['data'];
    }

    /**
     * 获取最近一条退货单
     * @param int $resultId
     * @param int $orderId
     * @param array $ary
     *
     * @return array $ret
     */
    public function getLastReturnOrder(int $orderId)
    {
        $url = "order/order-return/tmp-get-last-return-order";
        $param['uri'] = (string)$orderId;
        $ret = $this->httpPost($url, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    /**
     * 查询退款
     * @param int $orderId
     * @param string $uri
     * @param array $columns
     * @return array|void
     */
    public function getLastOrderRefund(int $orderId, string $uri, array $columns)
    {
        if (empty($orderId ?: $uri)) {
            return Response::byBus(Error::INVALID_ARGUMENT);
        }
        $param["orderId"] = $orderId;
        $param["columns"] = $columns;
        $param["uri"] = $uri;
        if ($error = $this->validate($param, [
            'columns' => 'required',
        ])) {
            return $error;
        }

        $ret = $this->httpPost("order/order-refund/tmp-get-last-order-refund", $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    /**
     *  获取订单退款，退货操作记录的接口
     */


    /**
     * 获取订单操作日志
     * @param string $uriOrId
     * @param array $filter
     * @return array
     */
    public function getOrderHandleRecordList($uriOrId, $filter = [])
    {
        return $this->orderHandlerRecordList($uriOrId, $filter);
    }

    private function orderHandlerRecordList($uriOrId, array $filter = [])
    {
        $params["uri"] = (string)$uriOrId;
        if (!empty($filter)) {
            $params["filter"] = $filter;
        }
        $this->validate($params, [
            'uri' => 'required',
        ]);
        $ret = $this->httpPost(Router::GET_ORDER_HANDLE_RECORD_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    private function dealRecordData($ret)
    {
        $data = $ret['data'] ?? [];
        if ($data) {
            $record = collect($data)->sortByDesc('createTime')->first();
            $remrk = json_decode($record['remark']);
            return [
                'orderId' => $record['orderId'],
                'reasonId' => $record['reasonId'],
                'reason' => $record['reason'],
                'createTime' => $record['createTime'],
                'refundFee' => get_property($remrk, 'refundFee', 0),
                'userName' => get_property($remrk, 'userName', ''),
                'telNumber' => get_property($remrk, 'telNumber', ''),
                'address' => get_property($remrk, 'address', ''),
                'com' => get_property($remrk, 'com', ''),
                'code' => get_property($remrk, 'code', ''),
            ];
        }
        return [];
    }

    /** 获取订单申请退款理由
     * @param $saleId
     * @return array
     * @throws \App\Exceptions\ApiException
     * @deprecated
     */
    public function getOrderRefundApplyReason($saleId)
    {
        return $this->getOrderRefundApplyRecord($saleId);
    }

    /**获取订单申请退款记录
     * @param $saleId
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function getOrderRefundApplyRecord($saleId)
    {
        $ret = $this->orderHandlerRecordList($saleId, [
            'title' => OrderReturnConst::ACTION_BUYER_APPLY_REFUND,
            'type' => '1',
        ]);
        if ($ret['code'] != 0) {
            CommonUtil::throwException(ErrorConst::ERROR_CODE, $ret['msg']);
        }
        return $this->dealRecordData($ret);
    }

    /**获取订单申请退货理由
     * @param $saleId
     * @return array
     * @throws \App\Exceptions\ApiException
     * @deprecated
     */
    public function getOrderReturnApplyReason($saleId)
    {
        return $this->getOrderReturnApplyRecord($saleId);
    }

    /**获取订单申请退货记录
     * @param $saleId
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function getOrderReturnApplyRecord($saleId)
    {
        $ret = $this->orderHandlerRecordList($saleId, [
            'type' => '2',
        ]);
        if ($ret['code'] != 0) {
            CommonUtil::throwException(ErrorConst::ERROR_CODE, $ret['msg']);
        }
        $data = get_property($ret, "data", []);
        if (!empty($data)) {
            $filtered = collect($data)->filter(function ($item) {
                return $item["title"] == OrderReturnConst::ACTION_BUYER_APPLY_RETURN
                    || $item["title"] == OrderReturnConst::ACTION_BUYER_APPLY_RETURN_REFUND_ONLY;
            });
            $ret['data'] = $filtered->toArray();
        }

        return $this->dealRecordData($ret);
    }

    /**
     * 获取买家退货信息
     * @param $saleId
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function getOrderReturnBuyerReturn($saleId)
    {
        $ret = $this->orderHandlerRecordList($saleId, [
            'title' => OrderReturnConst::ACTION_BUYER_RETURNING,
            'type' => '2',
        ]);
        if ($ret['code'] != 0) {
            CommonUtil::throwException(ErrorConst::ERROR_CODE, $ret['msg']);
        }
        return $this->dealRecordData($ret);
    }


    /** 获取订单拒绝退款信息
     * @param $saleId
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function getOrderRejectReturnRecord($saleId)
    {
        $ret = $this->orderHandlerRecordList($saleId, [
            'type' => '2',
        ]);
        if ($ret['code'] != 0) {
            CommonUtil::throwException(ErrorConst::ERROR_CODE, $ret['msg']);
        }

        $data = get_property($ret, "data", []);
        if (!empty($data)) {
            $filtered = collect($data)->filter(function ($item) {
                return $item["title"] == OrderReturnConst::ACTION_SELLER_REJECT_RETURN
                    || $item["title"] == OrderReturnConst::ACTION_SELLER_REJECT_RETURN_REFUND_ONLY;
            });
            $ret['data'] = $filtered->toArray();
        }
        return $this->dealRecordData($ret);
    }


    /**
     * 获取订单退货后退款的操作记录
     * @param $saleId
     * @param $resultId
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function getOrderAgreeReturnRefundRecord($saleId, $resultId)
    {
        $ret = $this->orderHandlerRecordList($saleId, [
            'title' => OrderReturnConst::ACTION_SELLER_AGREE_REFUND,
            'type' => '2',
            'resultId' => (string)$resultId
        ]);
        if ($ret['code'] != 0) {
            CommonUtil::throwException(ErrorConst::ERROR_CODE, $ret['msg']);
        }
        return $this->dealRecordData($ret);
    }

    /**
     * 获取订单开启退货后退款的操作记录
     * @param $saleId
     * @param $resultId
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function getOrderAfterAgreeReturnRecord($saleId, $resultId)
    {
        $ret = $this->orderHandlerRecordList($saleId, [
            'title' => OrderReturnConst::ACTION_DASHBOARD_AGREE_RETURN,
            'type' => '2',
            'resultId' => (string)$resultId
        ]);
        if ($ret['code'] != 0) {
            CommonUtil::throwException(ErrorConst::ERROR_CODE, $ret['msg']);
        }
        return $this->dealRecordData($ret);
    }
}
