<?php


namespace WptBus\Service\Order\Module;

use WptBus\Service\Order\Router;
use WptUtils\Arr;

class OrderSearch extends \WptBus\Service\BaseService
{
    /**
     * 获取orderIds
     * @param $winUserinfoId
     * @param $secCategoryId
     * @param $score
     * @param $limit
     * @param $timeLimit
     * @return array
     */
    public function searchSecCategoryIds($winUserinfoId, $timeLimit)
    {
        $params["winUserinfoId"] = (int)$winUserinfoId;
        $params["timeLimit"] = (int)$timeLimit;

        if ($error = $this->validate($params, [
            'winUserinfoId' => 'required|integer|min:1'
        ])) {
            return $error;
        }

        $ret = $this->httpPost(Router::SEARCH_SEC_CATEGORY_IDS, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });

        return $ret;
    }

    /**
     * 获取orderIds
     * @param $winUserinfoId
     * @param $secCategoryId
     * @param $score
     * @param $limit
     * @param $timeLimit
     * @return array
     */
    public function searchOrderIds($winUserinfoId, $secCategoryId, $score, $limit, $timeLimit)
    {
        $params["winUserinfoId"] = (int)$winUserinfoId;
        $params["secCategoryId"] = (int)$secCategoryId;
        $params["score"] = (string)$score;
        $params["limit"] = (int)$limit;
        $params["timeLimit"] = (int)$timeLimit;

        if ($error = $this->validate($params, [
            'winUserinfoId' => 'required|integer|min:1',
            'secCategoryId' => 'required|integer',
            'limit'=>'required|integer|min:1',
        ])) {
            return $error;
        }

        $ret = $this->httpPost(Router::SEARCH_ORDER_IDS, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });

        return $ret;
    }

    /**
     * 订单搜索
     * @param $fields
     * @param $where
     * @param $sort
     * @param $page
     * @param $pageSize
     * @return array
     */
    public function searchBuyerOrder(array $fields, $where, $sort, $page, $pageSize)
    {
        $params["fields"] = $fields;
        $params["where"] = json_encode($where, JSON_UNESCAPED_UNICODE);
        $params["sorts"] = json_encode($sort);
        $params["score"] = (string)$page;
        $params["limit"] = (int)$pageSize;

        if ($error = $this->validate($params, [
            'limit' => 'required|integer|min:1',
            'where' => 'required',
            'fields'=>'required|array'
        ])) {
            return $error;
        }

        $ret = $this->httpPost(Router::SEARCH_BUYER_ORDER_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 买家订单搜索
     * @param $score
     * @param $userinfoId
     * @param $winUserinfoId
     * @param $keyword
     * @param $status
     * @param $property
     * @param $startTime
     * @param $endTime
     * @param $size
     * @param $sortField
     * @param $sortType
     * @param $fields
     * @return array
     */
    public function searchBuyerOrderList(
        $score,
        $userinfoId,
        $winUserinfoId,
        $keyword,
        $status,
        $property,
        $startTime,
        $endTime,
        $size,
        $sortField,
        $sortType,
        $fields
    ) {
        $params["score"] = (string)$score;
        $params["userinfoId"] = (int)$userinfoId;
        $params["winUserinfoId"] = (int)$winUserinfoId;
        $params["keyword"] = (string)$keyword;
        $params["status"] = (int)$status;
        $params["property"] = (array)$property;
        $params["startTime"] = (int)$startTime;
        $params["endTime"] = (int)$endTime;
        $params["size"] = (int)$size;
        $params["sortField"] = (string)$sortField;
        $params["sortType"] = (string)$sortType;
        $params["fields"] = (array)$fields;

        if ($error = $this->validate($params, [
            'winUserinfoId' => 'required|integer|min:1',
            'fields'=>'required|array'
        ])) {
            return $error;
        }

        $ret = $this->httpPost(Router::SEARCH_BUYER_ORDER, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }
}
