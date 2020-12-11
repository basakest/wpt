<?php


namespace WptBus\Service\Order\Module;

use App\ConstDir\ErrorConst;
use App\Utils\CommonUtil;
use Monolog\Logger;
use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Lib\Validator;
use WptBus\Service\Order\Router;
use WptBus\Service\Order\Consts\OrderStatus;
use WptBus\Service\Order\Tools\Tools;
use WptCommon\Library\Facades\MLogger;
use WptUtils\Arr;

class Order extends \WptBus\Service\BaseService
{
    /**
     * @param string $saleUri
     * @return array
     */
    public function recoveryOrderByBuyerList(string $saleUri)
    {
        $params["saleUri"] = $saleUri;
        $ret = $this->httpPost(Router::RECOVERY_ORDER_BY_BUYER_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param string $saleUri
     * @return array
     */
    public function delOrderByBuyerList(string $saleUri)
    {
        $params["saleUri"] = $saleUri;
        $ret = $this->httpPost(Router::DEL_ORDER_BY_BUYER_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param int $winUserinfoId
     * @return array
     */
    public function getBuyerOrderCount(int $winUserinfoId)
    {
        $params["userId"] = $winUserinfoId;
        $ret = $this->httpPost(Router::GET_BUYER_ORDER_COUNT, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param int $userinfoId
     * @return array
     */
    public function getSellerOrderCount(int $userinfoId)
    {
        $params["userId"] = $userinfoId;
        $ret = $this->httpPost(Router::GET_SELLER_ORDER_COUNT, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    /**
     * 根据uri查询sale_order表
     * @param string $saleUri
     * @param array $fields
     * @param array $saleFields
     * @return array|void
     */
    public function getOrderByUriOrId(string $saleUri, array $fields, array $saleFields = [])
    {

        $params["uri"] = $saleUri;
        $params["fields"] = $fields;
        $params["saleFields"] = $saleFields;
        if ($error = $this->validate($params, ['uri' => 'required','fields'=>'required|array|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_ORDER_BY_URI, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data);
            $this->renameFields($data);
            return $data;
        });
        return $ret;
    }

    /**
     * 批量根据uri or id 查询sale 和 order
     * @param array $uriOrId
     * @param array $fields
     * @param array $saleFields
     * @return array
     */
    public function batchGetOrderByUriOrId(array $uriOrId, array $fields, array $saleFields = [])
    {
        $params["uriOrId"] = (array)$uriOrId;
        $params["fields"] = (array)$fields;
        $params["saleFields"] = (array)$saleFields;
        if ($error = $this->validate($params, ['uriOrId' => 'required|min:1|max:200','fields'=>'required|array|min:1'])) {
            MLogger::error("batchGetOrderByUriOrId", '查询订单个数超过200个了', [debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)]);
            return $error;
        }
        $newUriOrId =[];
        foreach($params["uriOrId"] as $v){
            $newUriOrId[] = (string)$v;
        }
        $params["uriOrId"] = $newUriOrId;
        $ret = $this->httpPost(Router::BATCH_GET_ORDER_BY_URI_OR_ID, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            foreach ((array)$data as $k => $v) {
                if ($v) {
                    $v = Arr::toObject($v);
                    $this->renameFields($v);
                    $data[$k] = $v;
                } else {
                    unset($data[$k]);
                }
            }
            return $data;
        });
        return $ret;
    }

    /**
     * 废弃方法 为了兼容老的数据结构
     * @param string $uriOrId
     * @param array $fields
     * @param array $saleFields
     * @return object
     * @deprecated
     */
    public function getOrderByUriOrIdCompatible(string $uriOrId, array $fields, array $saleFields = [])
    {
        $saleFields = $this->makeFields($saleFields);
        $ret = $this->getOrderByUriOrId($uriOrId, $fields, $saleFields);
        if ($ret["code"] != 0 || empty($ret["data"])) {
            return null;
        }
        $value = $this->compatibleField($ret["data"]);
        if (property_exists($value, "saleInfo") && !empty($value->saleInfo)) {
            if (property_exists($value->saleInfo, "content") && property_exists($value->saleInfo, "profile")) {
                $value->saleInfo->profile->content = $value->saleInfo->content;
            }
            if (property_exists($value->saleInfo, "draftId")) {
                $value->saleInfo->goodsId = $value->saleInfo->draftId;
            }
            $info =  Arr::toObject(array_merge((array)$value,(array)$value->saleInfo));
            unset($info->saleInfo);
            return $info;
        }
        return $value;
    }

    /**
     * 废弃方法 为了兼容老的数据结构
     * @param array $uriOrId
     * @param array $fields
     * @param array $saleFields
     * @return array
     * @deprecated
     */
    public function batchGetOrderByUriOrIdCompatible(array $uriOrId, array $fields, array $saleFields = [])
    {
        $saleFields = $this->makeFields($saleFields);
        $ret = $this->batchGetOrderByUriOrId($uriOrId,$fields,$saleFields);
        if($ret["code"] != 0){
            return null;
        }
        $newList = $this->makeData($ret["data"]);
        return $newList;
    }

    /**
     * 废弃方法 为了兼容老的数据结构
     * @param array $uriOrId
     * @param array $fields
     * @param array $saleFields
     * @param array $filter
     * @return array
     * @deprecated
     */
    public function getOrderListByUriOrIdCompatible(array $uriOrId, array $fields, array $saleFields = [], $filter = ['isDel' => 0])
    {
        $saleFields = $this->makeFields($saleFields);
        $ret = $this->batchGetOrderByUriOrId($uriOrId, $fields, $saleFields);
        if ($ret["code"] != 0) {
            return null;
        }
        $newList = $this->makeData($ret["data"]);
        return $this->filterResult($newList, $filter);
    }

    /**
     * 数组过滤
     * @param $result
     * @param array $filter
     * @return array|mixed|null
     */
    public function filterResult($result, $filter = [])
    {
        if (empty($result) || empty($filter)) {
            return $result;
        }

        if (!($isArr = is_array($result))) {
            $result = [$result];
        }
        foreach ($result as $key => $item) {
            foreach ($filter as $field => $value) {
                if (!property_exists($item, $field) or $item->$field != $value) {
                    unset($result[$key]);
                    break;
                }
            }
        }

        if (empty($result)) {
            return $isArr ? [] : null;
        }

        return $isArr ? array_values($result) : $result[0];
    }

    /**
     * 废弃方法 为了兼容老的数据结构 卖家订单列表
     * @param int $userinfoId
     * @param array $where
     * @param array $fields
     * @param array $saleFields
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     * @deprecated
     */
    public function getSellerOrderListCompatible(int $userinfoId,
        array $where,
        array $fields,
        array $saleFields = [],
        string $order = "",
        int $limit = 20,
        int $offset = 0)
    {
        $saleFields = $this->makeListFields($saleFields);
        if (in_array('saleId', $fields)) {
            $fields[] = 'orderId';
            $index = array_search('saleId', $fields);
            unset($fields[$index]);
        }
        if (in_array('saleType', $fields)) {
            $fields[] = 'orderType';
            $index = array_search('saleType', $fields);
            unset($fields[$index]);
        }
        $ret = $this->getSellerOrderList($userinfoId, $where, $fields, $saleFields, $order, $limit, $offset);

        if($ret["code"] != 0){
            return [];
        }
        $newList = $this->makeData($ret["data"]);
        return $newList;
    }

    /**
     * 废弃方法 为了兼容老的数据结构 买家订单列表
     * @param int $winUserinfoId
     * @param array $where
     * @param array $fields
     * @param array $saleFields
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     * @deprecated
     */
    public function getBuyerOrderListCompatible(int $winUserinfoId,
        array $where,
        array $fields,
        array $saleFields = [],
        string $order = "",
        int $limit = 20,
        int $offset = 0)
    {
        $saleFields = $this->makeListFields($saleFields);

        if (in_array('saleId', $fields)) {
            $fields[] = 'orderId';
            $index = array_search('saleId', $fields);
            unset($fields[$index]);
        }
        if (in_array('saleType', $fields)) {
            $fields[] = 'orderType';
            $index = array_search('saleType', $fields);
            unset($fields[$index]);
        }
        $ret = $this->getBuyerOrderList($winUserinfoId, $where, array_values($fields), $saleFields, $order, $limit, $offset);
        if($ret["code"] != 0){
            return [];
        }
        $newList = $this->makeData($ret["data"]);
        return $newList;
    }

    /**
     * tidb 命令脚本定制的方法 调用订单列表，业务不准调用这个方法
     * @param array $where
     * @param array $fields
     * @param array $saleFields
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param string $index
     * @return array
     * @deprecated
     */
    public function getOrderListByTidb(
        array $where,
        array $fields,
        array $saleFields = [],
        string $order = "",
        int $limit = 20,
        int $offset = 0,
        string $index = ""
    ) {
        if (!in_array($index, [
            '',
            'idx_saleType',
            'idx_status_delayReceiptTime_dispute_saleId',
            'idx_status_launchTime',
            'idx_status_isRated_finishedTime',
            'idx_userinfoId_endTime',
            'idx_userinfoId_finishedTime',
            'idx_userinfoId_paidtime',
            'idx_userinfoId_status',
            'idx_winUserinfoId_status'
        ])){
            return Response::byBus(Error::INVALID_ARGUMENT);
        }
        $saleFields = $this->makeListFields($saleFields);
        $params["fields"] = (array)$fields;
        $params["saleFields"] = (array)$saleFields;
        $params["order"] = (string)$order;
        $params["index"] = (string)$index;
        $params["limit"] = (int)$limit;
        $params["offset"] = (int)$offset;
        $params["where"] = json_encode($where, JSON_UNESCAPED_UNICODE);
        if ($error = $this->validate($params, [
            'where' => 'required',
            'fields' => 'required',
        ])) {
            return $error;
        }
        $ret = $this->setTimeout(8000)->httpPost(Router::GET_ORDER_LIST, $params);
        if($ret["code"] != 0){
            return [];
        }
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 0);
            foreach ((array)$data as $k => $v) {
                $this->renameFields($v);
                $data[$k] = $v;
            }
            return $this->makeData($data);
        });
        return $ret["data"];
    }

    /**
     * 卖家订单列表
     * @param int $userinfoId
     * @param array $where
     * @param array $fields
     * @param array $saleFields
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getSellerOrderList(
        int $userinfoId,
        array $where,
        array $fields,
        array $saleFields = [],
        string $order = "",
        int $limit = 20,
        int $offset = 0
    ) {
        $params["userinfoId"] = (int)$userinfoId;
        $params["fields"] = (array)$fields;
        $params["saleFields"] = (array)$saleFields;
        $params["order"] = (string)$order;
        $params["limit"] = (int)$limit;
        $params["offset"] = (int)$offset;
        $params["where"] = json_encode($where, JSON_UNESCAPED_UNICODE);
        if ($error = $this->validate($params, [
            'userinfoId' => 'required',
            'fields' => 'required',
        ])) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_SELLER_ORDER_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 0);
            foreach ((array)$data as $k => $v) {
                $this->renameFields($v);
                $data[$k] = $v;
            }
            return $data;
        });
        return $ret;
    }

    /**
     * 买家订单列表
     * @param int $winUserinfoId
     * @param array $where
     * @param array $fields
     * @param array $saleFields
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getBuyerOrderList(
        int $winUserinfoId,
        array $where,
        array $fields,
        array $saleFields = [],
        string $order = "",
        int $limit = 20,
        int $offset = 0
    ) {
        $params["winUserinfoId"] = (int)$winUserinfoId;
        $params["fields"] = (array)$fields;
        $params["saleFields"] = (array)$saleFields;
        $params["order"] = (string)$order;
        $params["limit"] = (int)$limit;
        $params["offset"] = (int)$offset;
        $params["where"] = json_encode($where, JSON_UNESCAPED_UNICODE);
        if ($error = $this->validate($params, [
            'winUserinfoId' => 'required',
            'fields' => 'required',
        ])) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_BUYER_ORDER_LIST, $params);
        $this->dealResultData($ret, function ($data) {

            $data = json_decode($data, 0);
            foreach ((array)$data as $k => $v) {
                $this->renameFields($v);
                $data[$k] = $v;
            }
            return $data;
        });
        return $ret;
    }

    /**
     * 卖家订单列表
     * @param int $userinfoId
     * @param array $where
     * @return array
     */
    public function getSellerOrderTotal(int $userinfoId, array $where )
    {
        $params["userinfoId"] = (int)$userinfoId;
        $params["where"] = json_encode($where,JSON_UNESCAPED_UNICODE);
        if ($error = $this->validate($params, [
            'userinfoId'=>'required',
        ])){
            return $error;
        }
        $ret = $this->httpPost(Router::GET_SELLER_ORDER_TOTAL, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 买家订单列表
     * @param int $winUserinfoId
     * @param array $where
     * @return array
     */
    public function getBuyerOrderTotal(int $winUserinfoId, array $where )
    {
        $params["winUserinfoId"] = (int)$winUserinfoId;
        $params["where"] = json_encode($where,JSON_UNESCAPED_UNICODE);
        if ($error = $this->validate($params, [
            'winUserinfoId'=>'required',
        ])){
            return $error;
        }
        $ret = $this->httpPost(Router::GET_BUYER_ORDER_TOTAL, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param $saleInfo
     * @return
     */
    private function renameFields(&$saleInfo)
    {

        if(!$saleInfo){
            return $saleInfo;
        }
        if (property_exists($saleInfo, "winJson")) {
            $saleInfo->win = json_decode($saleInfo->winJson);
        }
        if (property_exists($saleInfo, "saleInfo")) {
            if (property_exists($saleInfo->saleInfo, "profileJson")) {
                $saleInfo->saleInfo->profile = json_decode($saleInfo->saleInfo->profileJson);
            }
            if (property_exists($saleInfo->saleInfo, "priceJson")) {
                $saleInfo->saleInfo->price = json_decode($saleInfo->saleInfo->priceJson);
            }
            if (property_exists($saleInfo->saleInfo, "systemBzjJson")) {
                if (is_array($saleInfo->saleInfo->systemBzjJson)) {
                    $saleInfo->saleInfo->systemBzj = $saleInfo->saleInfo->systemBzjJson;
                    $saleInfo->saleInfo->systemBzjJson = json_encode($saleInfo->saleInfo->systemBzjJson);
                } else {
                    $saleInfo->saleInfo->systemBzj = !empty($saleInfo->saleInfo->systemBzjJson) ? json_decode($saleInfo->saleInfo->systemBzjJson) : "";
                }

            }
        }
    }

    /**
     * 延长付款期限
     * @param $orderId
     * @param $delayPayTime
     * @return array
     */

    public function setOrderDelayPayTime($orderId, $delayPayTime)
    {
        $data['delayPayTime'] = $delayPayTime;
        return $this->updateOrder($orderId, $data);
    }
    /**
     * 修改订单状态为未支付
     * @param int $orderId
     * @param array $data
     * @return array
     */

    public function updateOrderDeal(int $orderId, $data = [])
    {
        $data['status'] = 'deal';
        return $this->updateOrder($orderId, $data);
    }

    /**
     * 修改订单为流拍
     * @param int $orderId
     * @param array $data
     * @return array
     */
    public function updateOrderUnsold(int $orderId, $data = [])
    {
        $data['status'] = 'unsold';
        $ret = $this->updateOrder($orderId, $data);

        if ($ret['code'] != 0) {
            MLogger::error("OrderBus", "updateOrderUnsold 更新拍品报错", [$orderId, $ret]);
        }
        return $ret;
    }

    /**
     * 修改订单状态为已经支付
     * @param int $orderId
     * @param array $data
     * @return array
     */

    public function updateOrderPaid(int $orderId, $data = [])
    {
        $data['status'] = 'paid';
        return $this->updateOrder($orderId, $data);
    }

    /**
     * 修改订单状态为结束
     * @param int $orderId
     * @param array $data
     * @return array
     */

    public function updateOrderFinished(int $orderId, $data = [])
    {
        $data['status'] = 'finished';
        return $this->updateOrder($orderId, $data);
    }

    /**
     * 修改订单dispute值 默认是1 不能修改订单状态机
     * @param int $orderId
     * @param int $dispute
     * @param array $data
     * @return array
     */
    public function updateOrderDispute(int $orderId, $dispute = 1, $data = [])
    {

        if(isset($data['status'])){
            return Response::byBus(Error::INVALID_ARGUMENT);
        }
        $data['dispute'] = $dispute;
        return $this->updateOrder($orderId, $data);
    }

    /**
     * 修改订单WINJSON值 不能修改订单状态机
     * @param int $orderId
     * @param object|array $win
     * @param array $data
     * @return array
     */
    public function updateOrderWinJson(int $orderId, $win, $data = [])
    {
        if (!is_object($win) && !is_array($win)) {
            return Response::byBus(Error::INVALID_ARGUMENT);
        }
        if(isset($data['status'])){
            return Response::byBus(Error::INVALID_ARGUMENT);
        }
        $data['winJson'] = json_encode($win, JSON_UNESCAPED_UNICODE);
        return $this->updateOrder($orderId, $data);
    }

    /**
     * 更新订单
     * @param int $orderId
     * @param array $data
     * @return array
     * @deprecated
     */
    public function updateOrder(int $orderId, array $data)
    {

        $params["orderId"] = $orderId;
        $params["data"] = $data;
        $this->validate($params, [
            'orderId'=>'required',
            'data'=>'required',
        ]);
        if (isset($data["status"])) {
            if (!is_numeric($data["status"])){
                $data["status"] = OrderStatus::ORDER_STATUS_MAP[$data["status"]];
            }
        }
        if (isset($data["unsoldReason"])) {
            if (!is_numeric($data["unsoldReason"])){
                $data["unsoldReason"] = OrderStatus::ORDER_UNSOLD_REASON_MAP[$data["unsoldReason"]];
            }
        }
        foreach ((array)$data as $k=>$v){
            $data[$k] = (string)$v;
        }
        $params["data"] = $data;
        $ret = $this->httpPost(Router::UPDATE_ORDER, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 创建订单
     * @param array $data
     * @return array
     */
    public function createOrder(array $data)
    {
        $this->validate($data, [
            'orderId'=>'required',
            'userinfoId'=>'required',
            'winUserinfoId'=>'required',
            'winJson'=>'required',
        ]);
        $data["orderId"] = (int)$data["orderId"];
        $data["userinfoId"] = (int)$data["userinfoId"];
        $data["winUserinfoId"] = (int)$data["winUserinfoId"];
        if (isset($data["delayPayTime"])) {
            $data["delayPayTime"] = (int)$data["delayPayTime"];
        }
        if (isset($data["endTime"])) {
            $data["endTime"] = (int)$data["endTime"];
        }
        if (isset($data["saleType"])) {
            $data["saleType"] = (int)$data["saleType"];
        }
        for ($i = 0; $i < 2; $i++) {
            $ret = $this->httpPost(Router::CREATE_ORDER, $data);
            $this->dealResultData($ret, function ($data) {
                $data = json_decode($data, 1);
                return $data;
            });
            if ($ret["code"] == 0){
                return $ret;
            }
            if ($i == 1){
                return $ret;
            }
        }
    }

    /**
     * @param $data
     * @return array
     */
    private function makeData($data){
        $newList = [];
        foreach ((array)$data as $key=>$value){
            $value = $this->compatibleField($value);
            if (property_exists($value, "saleInfo") && !empty($value->saleInfo)) {
                if (property_exists($value->saleInfo, "content") && property_exists($value->saleInfo, "profile")) {
                    $value->saleInfo->profile->content = $value->saleInfo->content;
                }
                if (property_exists($value->saleInfo, "draftId")) {
                    $value->saleInfo->goodsId = $value->saleInfo->draftId;
                }
                $newList[$key] = Arr::toObject(array_merge((array)$value,(array)$value->saleInfo));
            }else{
                $newList[$key] = $value;
            }
            unset($newList[$key]->saleInfo);
        }
        return $newList;
    }

    /**
     * @param $value
     * @return object
     */
    private function compatibleField($value){
        if (isset($value->status)) {
            $value->status = Tools::getOrderStatusText((int)$value->status);
            unset($value->saleInfo->status);
        }
        if (isset($value->unsoldReason)) {
            $value->unsoldReason = Tools::getUnsoldReasonText((int)$value->unsoldReason);
        }
        if (isset($value->saleType)) {
            $value->type = $value->saleType;
        }
        if (isset($value->orderType)) {
            $value->type = $value->orderType;
            $value->saleType = $value->orderType;
        }
        if (isset($value->orderId)) {
            $value->saleId = $value->orderId;
            $value->id = $value->orderId;
        }
        if (isset($value->saleId)) {
            $value->id = $value->saleId;
        }
        return $value;
    }

    private function makeFields($saleFields){
        if (count($saleFields) > 0) {
            if (in_array('profileJson', $saleFields)) {
                $saleFields[] = 'content';
            }
            if (!in_array('id', $saleFields)) {
                $saleFields[] = 'id';
            }
            if (!in_array('uri', $saleFields)) {
                $saleFields[] = 'uri';
            }
            if (!in_array('isDel', $saleFields)) {
                $saleFields[] = 'isDel';
            }
        }
        return $saleFields;
    }

    private function makeListFields($saleFields){
        if (count($saleFields) > 0) {
            if (in_array('profileJson', $saleFields)) {
                $saleFields[] = 'content';
            }
            if (!in_array('id', $saleFields)) {
                $saleFields[] = 'id';
            }
        }
        return $saleFields;
    }

    /**
     * @param string $uri
     * @param string $from
     * @param int $winUserinfoId
     * @param int $finishTime
     * @return array|void
     */
    public function orderReceipt(string $uri, string $from, int $winUserinfoId, int $finishTime, $fixData = false)
    {
        $param["uri"] = $uri;
        $param["from"] = $from;
        $param["loginUserId"] = $winUserinfoId;
        $param["finishTime"] = $finishTime;
        if ($error = $this->validate($param, [
            'uri'=>'required',
            'from'=>'required',
            'loginUserId'=> 'required',
            'finishTime'=>'required',
        ])){
            return $error;
        }
        $route = Router::ORDER_RECEIPT;
        if ($fixData) {
            $route = Router::ORDER_RECEIPT_FIX;
        }
        $ret = $this->httpPost($route, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param int $orderId
     * @param int $createTime
     * @return array|void
     */
    public function applyFacetrade(int $orderId, int $createTime)
    {
        $param['orderId'] = $orderId;
        $param["createTime"] = $createTime;

        if ($error = $this->validate($param, [
            'orderId'=>'required',
            'createTime'=>'required',
        ])){
            return $error;
        }
        $ret = $this->httpPost(Router::APPLY_FACETRADE, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param string $uri
     * @param int $userinfoId
     * @return array|void
     */
    public function facetradeCheck(string $uri, int $userinfoId)
    {
        $param['uri'] = $uri;
        $param["loginUserId"] = $userinfoId;

        if ($error = $this->validate($param, [
            'uri'=>'required',
            'loginUserId'=> 'required',
        ])){
            return $error;
        }
        $ret = $this->httpPost(Router::FACETRADE_CHECK, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param string $uri
     * @param int $winUserinfoId
     * @return array|void
     */
    public function delayReceipt(string $uri, int $winUserinfoId)
    {
        $param["uri"] = $uri;
        $param["loginUserId"] = $winUserinfoId;
        $param["delayDays"] = (int)7;
        if ($error = $this->validate($param, [
            'uri'=>'required',
            'loginUserId'=> 'required',
        ])){
            return $error;
        }
        $ret = $this->httpPost(Router::DELAY_RECEIPT, $param);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    public function getRelationOrderList(int $saleId, array $fields, int $page = 1, int $pageSize = 20)
    {
        $offset = ($page - 1) * $pageSize;
        $params['saleId'] = $saleId;
        $params['fields'] = $fields;
        $params['offset'] = $offset;
        $params['limit'] = $pageSize;
        if ($error = $this->validate($params, [
            'saleId'=>'required',
            'fields'=> 'required',
            'limit' => 'required',
        ])){
            return $error;
        }
        $ret = $this->httpPost(Router::GET_ORDER_LIST_BY_PID, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


}
