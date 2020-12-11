<?php

namespace WptBus\Service\Sale\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Sale\Router;

class Bid extends BaseService
{
    /**
     * 插入出价 （临时接口，不要使用）
     * @param $saleId
     * @param $userinfoId
     * @param $price
     * @param $nickname
     * @param $headimagurl
     * @param $saleType
     * @return array
     */
    public function insertBid(int $saleId, int $userinfoId, int $price, $nickname, $headimagurl, int $saleType)
    {
        $data = [
            'SaleID' => $saleId,
            'UserInfoID' => $userinfoId,
            'Price' => $price,
            'Nickname' => $nickname,
            'HeadImagURL' => $headimagurl,
            'SaleType' => $saleType
        ];

        return $this->httpPost(Router::BID_INSERT_BID, $data);
    }


    /**
     * 同步拍出价
     * @param int $saleId
     * @param int $price
     * @param int $userinfoId
     * @return array
     */
    public function SyncAuctionToBid(int $saleId, int $price, int $userinfoId)
    {
        $data = [
            'SaleId' => $saleId,
            'Price' => $price,
            'UserInfoID' => $userinfoId,
        ];

        $result = $this->httpPost(Router::SYNC_AUCTION_TO_BID, $data);
        $this->dealResultData($result, $this->formatResult());


        return $result;
    }

    /**
     * 出价退回
     * @param int $saleId
     * @param int $userinfoId
     * @return array
     */
    public function BidBackOff(int $saleId, int $userinfoId)
    {
        $data = [
            'SaleID' => $saleId,
            'UserInfoID' => $userinfoId,
        ];

        $result = $this->httpPost(Router::BID_BACK_OFF, $data);
        $this->dealResultData($result, function ($data) {
            return $data;
        });

        return $result;
    }


    /**
     * @param $saleId
     * @return array
     */
    public function getTopPriceAndBidNum($saleId)
    {
        $data = [
            'SaleId' => (int) $saleId,
        ];

        $result = $this->httpPost(Router::GET_TOP_PRICE_AND_BID_NUM, $data);
        $this->dealResultData($result, function ($data) {
            return $data;
        });

        return $result;
    }

    /**
     * @param $saleId
     * @return array
     */
    public function getSaleBidCount($saleId)
    {
        $data = [
            'SaleId' => (int) $saleId,
        ];

        $result = $this->httpPost(Router::GET_SALE_BID_COUNT, $data);
        $this->dealResultData($result, function ($data) {
            return $data;
        });

        return $result;
    }

    /**
     * @param $saleId
     * @return array
     */
    public function getMaxPrice($saleId)
    {
        $data = [
            'SaleId' => (int) $saleId,
        ];

        $result = $this->httpPost(Router::GET_MAX_PRICE, $data);
        $this->dealResultData($result, function ($data) {
            return $data;
        });

        return $result;
    }

    /**
     * 拍品的某次出价，是否为指定用户出的
     * @param int $userinfoId
     * @param int $saleId
     * @param int $price
     * @return array ['code' => 0, 'msg' => '', data => ['hasBid' => true]]
     */
    public function hasBid(int $userinfoId, int $saleId, int $price)
    {
        $data = [
            'SaleId' => $saleId,
            'UserInfoId' => $userinfoId,
            "Price" => $price
        ];

        $result = $this->httpPost(Router::BID_HAS_BID, $data);

        return $result;
    }

    /**
     * 获取拍品聚合信息
     * fields支持的属性 ['topPrice', 'hasHistoryBid', 'hasSaleBid', 'bidNum']
     * @param int $saleId
     * @param int $userinfoId
     * @param array $fields
     * @return array
     */
    public function info(int $saleId, int $userinfoId, array $fields)
    {
        $data = [
            'SaleId' => $saleId,
            'UserInfoId' => $userinfoId,
            "Fields" => $fields
        ];

        $result = $this->httpPost(Router::BID_INFO, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 出价
     * @param int $saleId
     * @param int $userinfoId
     * @param int $price
     * @return array ['code' => 0, 'msg' => '', data => ['bidId' => 123, 'saleEndTime' => '']]
     */
    public function toBid(int $saleId, int $userinfoId, int $price)
    {
        $data = [
            'SaleId' => $saleId,
            'UserInfoId' => $userinfoId,
            'Price' => $price
        ];

        $result = $this->httpPost(Router::BID_TO_BID, $data);
        $this->dealResultData($result, $this->formatResult());

        if ($result['code'] > 0) {
            $result['code'] = $this->toBidErrMap($result['code']);
        }

        return $result;
    }

    /**
     * 用户历史是否出过价
     * @param int $userinfoId
     * @return array ['code' => 0, 'msg' => '', data => ['hasBid' => true]]
     */
    public function hasHistoryBid(int $userinfoId)
    {
        $data = [
            'UserInfoId' => $userinfoId,
        ];

        return $this->httpPost(Router::BID_HAS_HISTORY_BID, $data);
    }

    /**
     * 获取拍品出价列表(带聚合数据)
     * @param int $saleId
     * @param array $fields
     * @param string $score
     * @param int $limit
     * @param string $orderBy 默认从指定score倒序取，【asc按照正序取】
     * @return array
     */
    public function getSaleBidDetailList(int $saleId, array $fields, string $score = '', int $limit = 10, string $orderBy = 'desc')
    {
        $data = [
            'SaleId' => (int) $saleId,
            'Columns' => $fields,
            'Score' => $score,
            'Limit' => $limit,
            'OrderBy' => $orderBy,
        ];

        $result = $this->httpPost(Router::GET_SALE_BID_DETAIL_LIST, $data);
        $this->dealResultData($result, function ($data) {
            if (!empty($data) && is_string($data)) {
                return json_decode($data, true);
            }
            return [];
        });

        return $result;
    }

    /**
     * @param $saleId
     * @param $fields
     * @param $score
     * @param $limit
     * @return array
     */
    public function getSaleBidList($saleId, $fields = [], $score = '', $limit = 10)
    {
        $data = [
            'SaleId' => (int) $saleId,
            'Columns' => $fields,
            'Score' => $score,
            'Limit' => $limit,
        ];

        $result = $this->httpPost(Router::GET_SALE_BID_LIST, $data);
        $this->dealResultData($result, function ($data) {
            if (!empty($data) && is_string($data)) {
                return json_decode($data, true);
            }
            return [];
        });

        return $result;
    }

    /**
     * 用户是否对拍品出过价
     * @param int $userinfoId
     * @param int $saleId
     * @return array  ['code' => 0, 'msg' => '', data => ['hasBid' => true]]
     */
    public function hasSaleBid(int $userinfoId, int $saleId)
    {
        $data = [
            'SaleId' => $saleId,
            'UserInfoId' => $userinfoId,
        ];

        $result = $this->httpPost(Router::BID_HAS_SALE_BID, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 获取用户7天出价拍品数量
     * @param int $userinfoId
     * @return array  ['code' => 0, 'msg' => '', data => 0]
     */
    public function get7DaySaleCount($userinfoId)
    {
        $data = [
            "userInfoId" => (int) $userinfoId,
        ];

        $result = $this->httpPost(Router::BID_GET_7_DAY_SALE_COUNT, $data);

        $this->dealResultData($result, function ($data) {
            return $data;
        });

        return $result;
    }

    /**
     * 批量获取拍品最高出价
     * @param array $saleIds
     * @return array
     */
    public function batchGetMaxPrice(array $saleIds)
    {
        $saleIds = array_map('intval', $saleIds);

        $data = [
            "saleIds" => $saleIds,
        ];

        $result = $this->httpPost(Router::BID_BATCH_GET_MAX_PRICE, $data);

        $this->dealResultData($result, function ($data) {
            if (!empty($data) && is_string($data)) {
                $idMap = [];
                foreach (json_decode($data, true) as $saleBid) {
                    $idMap[strval($saleBid['saleId'])] = $saleBid['maxPrice'];
                }

                return $idMap;
            }
            return [];
        });

        return $result;
    }

    /**
     * 批量获取拍品出价次数
     * @param array $saleIds
     * @return array
     */
    public function batchGetSaleBidCount(array $saleIds)
    {
        $saleIds = array_map('intval', $saleIds);

        $data = [
            "saleIds" => $saleIds,
        ];

        $result = $this->httpPost(Router::BID_BATCH_GET_SALE_BID_COUNT, $data);

        $this->dealResultData($result, function ($data) {
            if (!empty($data) && is_string($data)) {
                $idMap = [];
                foreach (json_decode($data, true) as $saleBid) {
                    $idMap[strval($saleBid['saleId'])] = $saleBid['bidCount'];
                }

                return $idMap;
            }
            return [];
        });

        return $result;
    }

    /**
     * 批量获取拍品出价列表
     * @param array $saleIds
     * @param array $fields
     * @param string $score
     * @param int $limit
     * @return array
     */
    public function batchGetSaleBidList(array $saleIds, $fields = [], $score = '', $limit = 10)
    {
        $saleIds = array_map('intval', $saleIds);

        $data = [
            'saleIds' => $saleIds,
            "columns" => (array) $fields,
            "score" => (string) $score,
            "limit" => (int) $limit,
        ];

        $result = $this->httpPost(Router::BID_BATCH_GET_SALE_BID_LIST, $data);

        $this->dealResultData($result, function ($data) use ($fields) {
            if (!empty($data) && is_string($data)) {
                $idMap = [];

                foreach (json_decode($data, true) as $saleBid) {
                    $saleId = strval($saleBid['saleId']);

                    if (!empty($fields)) {
                        $newSaleBid = [
                            'score' => $saleBid['score'] ?? '',
                        ];

                        foreach ($fields as $field) {
                            if (isset($saleBid[$field])) {
                                $newSaleBid[$field] = $saleBid[$field];
                            }
                        }

                        $saleBid = $newSaleBid;
                    }
                    $idMap[$saleId][] = json_decode(json_encode($saleBid));
                }
                return $idMap;
            }
            return [];
        });

        return $result;
    }

    /**
     * 获取用户出价拍品列表,包含订单信息,可能废弃
     * @param int $userInfoId
     * @param array $fields
     * @param string $scoreMin
     * @param string $scoreMax
     * @param int $limit
     * @return array
     */
    public function getBidSaleList($userInfoId, $fields = [], $scoreMin = '', $scoreMax = '', $limit = 10)
    {
        $data = [
            'userInfoId' => (int) $userInfoId,
            'columns' => $fields,
            'scoreMin' => (string) $scoreMin,
            'scoreMax' => (string) $scoreMax,
            'limit' => (int) $limit,
        ];

        $result = $this->httpPost(Router::GET_BID_SALE_LIST, $data);
        $this->dealResultData($result, function ($data) {
            if (!empty($data) && is_string($data)) {
                $res = json_decode($data, true);
                foreach ($res as &$sale) {
                    if (isset($sale['saleOrder'])) {
                        if (!$sale['saleOrder']['status']) {
                            unset($sale['saleOrder']);
                        }
                    }

                    if (isset($sale['priceJson'])) {
                        $sale['price'] = json_decode($sale['priceJson'], true);
                        unset($sale['priceJson']);
                    }

                    if (isset($sale['profileJson'])) {
                        $sale['profile'] = json_decode($sale['profileJson'], true);
                        unset($sale['profileJson']);
                    }
                }
                return $res;
            }
            return [];
        });

        return $result;
    }

    /**
     * 获取用户7天内(外)出价拍品列表
     * @param int $type
     * @param int $userInfoId
     * @param array $fields
     * @param string $score
     * @param int $limit
     * @return array
     */
    public function getSevenDaysBidSaleList($type, $userInfoId, $fields = [], $score = '', $limit = 10)
    {
        $data = [
            'type' => (int) $type,
            'userInfoId' => (int) $userInfoId,
            'columns' => $fields,
            'score' => (string) $score,
            'limit' => (int) $limit,
        ];

        $result = $this->httpPost(Router::GET_SEVEN_DAYS_BID_SALE_LIST, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 出价聚合接口
     * @param int $userinfoId
     * @param array $saleIds
     * @param array $fields 需要的聚合字段
     * @param int $bidListLimit
     * @return array
     */
    public function batchInfo($userInfoId, $saleIds, $fields, $bidListLimit)
    {
        $data = [
            'UserInfoId' => intval($userInfoId),
            'SaleIds' => $saleIds,
            'Fields' => $fields,
            'BidListLimit' => $bidListLimit,
        ];

        $result = $this->httpPost(Router::BID_BATCH_INFO, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 出价错误码映射
     * @param $code
     * @return int
     */
    private function toBidErrMap($code)
    {
        $map = [
            201089 => 556,
            201085 => 303,
            201075 => 303,
            201090 => 430,
            201087 => 415,
            201088 => 418,
            201076 => 422,
            201086 => 402,
            201077 => 428,
            201078 => 416,
            201079 => 417,
            201080 => 431,
            201081 => 405,
            201082 => 421,
            201083 => 419,
            201084 => 420,
        ];

        if (isset($map[$code])) {
            return $map[$code];
        }

        return $code;
    }

    protected function formatResult()
    {
        return function ($data) {
            if (!empty($data) && is_string($data)) {
                return json_decode($data, true);
            }
            return [];
        };
    }

    /**
     * 获取用户出价拍品列表
     * @param int $userInfoId
     * @param array $fields
     * @param string $score
     * @param int $limit
     * @return array
     */
    public function getUserBidSaleList($userInfoId, $fields = [], $score = '', $limit = 10)
    {
        $data = [
            'userInfoId' => (int) $userInfoId,
            'columns' => $fields,
            'score' => (string) $score,
            'limit' => (int) $limit,
        ];

        $result = $this->httpPost(Router::GET_USER_BID_SALE_LIST, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 获取在拍的用户出价拍品列表
     * @param int $userInfoId
     * @param array $saleIds
     * @param array $fields
     * @return array
     */
    public function getInSaleUserBidSaleList($userInfoId, $saleIds, $fields = [])
    {
        $data = [
            'userInfoId' => (int) $userInfoId,
            'columns' => $fields,
            'saleIds' => $saleIds,
        ];

        $result = $this->httpPost(Router::GET_IN_SALE_USER_BID_SALE_LIST, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 获取截拍的用户出价拍品列表
     * @param int $userInfoId
     * @param array $fields
     * @param string $score
     * @param string $endScore
     * @param int $limit
     * @return array
     */
    public function getInDealUserBidSaleList($userInfoId, $fields = [], $score = '', $endScore = '', $limit = 10)
    {
        $data = [
            'userInfoId' => (int) $userInfoId,
            'columns' => $fields,
            'score' => (string) $score,
            'endScore' => (string) $endScore,
            'limit' => (int) $limit,
        ];

        $result = $this->httpPost(Router::GET_IN_DEAL_USER_BID_SALE_LIST, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 用户出价列表中是否有竞拍中的拍品
     * @param int $userinfoId
     * @return array ['code' => 0, 'msg' => '', data => ['hasBid' => true]]
     */
    public function hasUserBidInSale(int $userinfoId)
    {
        $data = [
            'userInfoId' => $userinfoId,
        ];

        $result = $this->httpPost(Router::HAS_USER_BID_IN_SALE, $data);

        return $result;
    }

    /**
     * @param $userInfoId
     * @param $days
     * @return array
     */
    public function hasBidByDays(int $userInfoId, int $days)
    {
        $data = [
            'UserInfoId' => $userInfoId,
            "Days" => $days
        ];

        $result = $this->httpPost(Router::BID_HAS_BID_BY_DAY, $data);

        return $result;
    }

    /**
     * 批量获取拍品最高出价
     * @param array $saleIds
     * @return array
     */
    public function multiGetTopPriceAndBidNum(array $saleIds)
    {
        $saleIds = array_map('intval', $saleIds);
        $data = [
            "saleIds" => $saleIds,
        ];
        $result = $this->httpPost(Router::MULTI_GET_TOP_PRICE_AND_BID_NUM, $data);

        $this->dealResultData($result, function ($data) {
            if (!empty($data) && is_string($data)) {
                $idMap = [];
                foreach (json_decode($data, true) as $saleBid) {
                    $idMap[strval($saleBid['saleId'])]['topPrice'] = $saleBid['maxPrice'];
                    $idMap[strval($saleBid['saleId'])]['bidCount'] = $saleBid['bidCount'];
                }

                return $idMap;
            }
            return [];
        });

        return $result;
    }
}
