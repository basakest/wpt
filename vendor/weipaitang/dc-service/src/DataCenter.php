<?php

namespace WptDataCenter;

use InvalidArgumentException;
use Throwable;
use WptDataCenter\Concerns\DataCenterTraits;
use WptDataCenter\Exception\DataCenterException;
use WptDataCenter\Handler\CurlHandler;
use WptDataCenter\Route\Route;

/**
 * Class DataCenter
 * @package WptDataCenter
 */
class DataCenter
{
    /**
     * mixin
     */
    use DataCenterTraits;

    /**
     * @var $intance
     */
    protected static $instance;

    /**
     * @var bool
     */
    private $defaultVal = false;

    /**
     * @var array
     */
    private $dayRange = [];

    /**
     * @return DataCenter
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function setDefault()
    {
        $this->defaultVal = true;
        return $this;
    }

    /**
     * @param int $uid
     * @param array $fields
     * @param int $retries
     * @return array
     * @throws DataCenterException | Throwable
     */
    public function get(int $uid, array $fields, $retries = 1): array
    {
        $fields = array_values(array_unique($fields));
        $data = CurlHandler::getInstance()->setRetries($retries)->go(Route::REPORT_LIST, [
            "uid" => $uid,
            "properties" => $fields,
        ]);
        $result = $data["data"] ?? [];
        if ($this->defaultVal) {
            foreach ($fields as $field) {
                if (!isset($result[$field])) {
                    $result[$field] = 0;
                }
            }
        }
        $this->defaultVal = false;
        return $result;
    }

    /**
     * @param array $ids
     * @param array $fields
     * @param int $retries
     * @return array
     * @throws DataCenterException | Throwable
     */
    public function batchGet(array $ids, array $fields, $retries = 1): array
    {
        $result = [];
        $uids = array_map(function ($item) {
            return intval($item);
        }, $ids);
        $data = CurlHandler::getInstance()->setRetries($retries)->go(Route::REPORT_BATCH_LIST, [
            "uids" => $uids,
            "fields" => $fields
        ]);
        if (isset($data["data"])) {
            $result = json_decode($data["data"], true);
        }
        return $result;
    }

    /**
     * @param int $id
     * @param int $retries
     * @return array
     * @throws Throwable
     */
    public function command(int $id, $retries = 1): array
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(Route::REPORT_COMMAND, [
            "id" => $id
        ]);
    }


    /**
     * 根据日期获取数据 当key长度等于=1直接返回结果
     * @param int $uid
     * @param array $key
     * @param string $date
     * @param int $retries
     * @return array|string
     * @throws Throwable
     */
    public function getByDate(int $uid, array $key, string $date = "", $retries = 1)
    {
        if ($uid <= 0 || strlen($uid) > 9 || count($key) == 0) {
            throw new InvalidArgumentException("参数错误");
        }
        if ($date == "") {
            $date = date("Ymd");
        }
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::SUBSCRIBE_GET, [
            "uid" => $uid,
            "key" => $key,
            "date" => $date,
        ]);
        return $result["data"] ?? [];
    }


    /*
    DataCenter::getInstance()->getByDate(2, ['group_buy_order', 'group_buy_price'], ['20191126', '20191127']);
    [
        'group_buy' => [
            '20191127' => 30,
            '20191126' => 10,
        ],
        'group_buy_price' => [
            '20191127' => 31,
            '20191126' => 11,
        ],
    ]
     */
    /**
     * 获取多个key 多个时间的数据
     * @param int $uid
     * @param array $key
     * @param array $date
     * @param int $retries
     * @return array
     * @throws Throwable
     */
    public function multiGetByDate(int $uid, array $key, array $date = [], $retries = 1)
    {
        if ($uid <= 0 || strlen($uid) > 9 || count($key) == 0) {
            throw new InvalidArgumentException("参数错误");
        }
        if (count($date) == 0 && count($this->dayRange) == 0) {
            throw new InvalidArgumentException("日期区间无效");
        }
        $date = $this->dayRange ? array_values($this->dayRange) : $date;
        $this->dayRange = [];
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::SUBSCRIBE_MUTLI_GET, [
            "uid" => $uid,
            "key" => $key,
            "date" => $date,
        ]);
        $data = $result["data"] ?? '';
        return json_decode($data, true);
    }


    /*
    [
        'group_buy' => [
            '20191127' => 30,
            '20191126' => 10,
        ],
        'group_buy_price' => [
            '20191127' => 31,
            '20191126' => 11,
        ],
    ]

     ['group_buy' => 40, 'group_buy_price' => 42]

     */
    /**
     * 获取多个key 多个时间的数据 并计算总和
     * @param int $uid
     * @param array $key
     * @param array $date
     * @param int $retries
     * @return array
     * @throws Throwable
     */
    public function multiGetAndCountByDate(int $uid, array $key, array $date = [], $retries = 1)
    {
        try {
            $result = $this->multiGetByDate($uid, $key, $date, $retries);
            $data = [];
            foreach ($result as $k => $val) {
                $total = 0;
                foreach ($val as $num) {
                    $total += intval($num);
                }
                $data[$k] = $total;
            }
            return $data;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param string $date (Ymd,YmdH)两种格式
     * @param $key
     * @param int $start
     * @param int $end
     * @param int $retries
     * @return array
     * @throws Throwable
     */
    public function getSubscribeSortDataRank(string $date, $key, int $start, int $end, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::SUBSCRIBE_SORT_DATA_RANK, [
            "date" => $date,
            "key" => $key,
            "start" => $start,
            "end" => $end
        ]);
        $data = $result["data"] ?? [];
        $rank = [];
        foreach ($data as $value) {
            $uid = $value["member"] ?? 0;
            $score = $value["score"] ?? 0;
            if ($uid > 0) {
                $rank[$uid] = $score;
            }
        }
        return $rank;
    }

    /**
     * @param string $date (Ymd)
     * @param $key
     * @param $userIds
     * @param int $retries
     * @return array
     * @throws Throwable
     */
    public function batchGetSubscribeSortData(string $date, $key, array $userIds, $retries = 1)
    {
        $ids = [];
        foreach ($userIds as $value) {
            $ids[] = intval($value);
        }
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BATCH_SUBSCRIBE_SORT_DATA, [
            "date" => $date,
            "key" => $key,
            "userIds" => $ids
        ]);
        return $result["data"] ?? [];
    }

    /**
     * @param int $uid
     * @param int $retries
     * @return mixed
     * @throws Throwable
     */
    public function hasBid15Day(int $uid, int $retries = 1): bool
    {

        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::HAS_BID_15DAY, [
            "uid" => intval($uid)
        ]);
        $isBid = $result["isBid"] ?? false;
        return $isBid;
    }

    /**
     * @param string $business
     * @param string $uniqueId 用户唯一标识（uid or uri）
     * @param int $retries
     * @return bool
     * @throws Throwable
     */
    public function existUserInNameList(string $business, $uniqueId, $retries = 1): bool
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::EXIST_USER_IN_NAME_LIST, [
            "business" => $business,
            "uid" => strval($uniqueId)
        ]);
        return $result["exist"] ?? false;
    }


    public function appendBusinessNameList(string $business, $uniqueId, int $expireTime, $desc = "", $retries = 1): bool
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::APPEND_NAME_LIST, [
            "business" => $business,
            "uniqueId" => strval($uniqueId),
            "desc" => $desc,
            "expireTime" => $expireTime
        ]);
        $code = $result["code"] ?? "";
        return ($code == 0) ? true : false;
    }

    public function getUserNameListExpire(string $buiness, $uniueId, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::GET_USER_EXPIRE_NAME_LIST, [
            "business" => $buiness,
            "uniqueId" => strval($uniueId)
        ]);
        $exist = $result["exist"] ?? false;
        $expireTime = $result["expireTime"] ?? 0;
        return [$exist, $expireTime];
    }

    public function buyerIndex(int $userId, int $shopId, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BUYER_INDEX, [
            "uid" => intval($userId),
            "shopId" => intval($shopId)
        ]);
        return $result["data"] ?? [];
    }

    public function getBigDataList(string $table, int $lastId, array $where, int $pageSize = 1000, string $date = "", int $retries = 1)
    {
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $where[$key] = strval($value);
            }
        } else {
            $where["dcxxx"] = "1"; // TODO goproto where 为map类型；先临时解决
        }
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_LIST, [
            "table" => strval($table),
            "lastId" => intval($lastId),
            "where" => $where,
            "date" => strval($date),
            "pageSize" => intval($pageSize),
        ]);
        if (isset($result["data"]) && !empty($result["data"])) {
            $result["data"] = json_decode($result["data"], true);
        } else {
            $result["data"] = [];
        }
        return $result;
    }

    public function doubleFlowBringVisitAndDeal(int $shopId, string $date, string $type, int $retries = 1)
    {
        $params = [
            "shopId" => intval($shopId),
            "date" => strval($date),
            "type" => strval($type)
        ];
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::DOUBLE_FLOW_VISIT_DEAL, $params);
        return $result["data"] ?? [];
    }


    // 广告二级类目喜好缓存(数据较少)
    public function categoryLikes($retries = 1)
    {
        $table = "api_seccategory_user_cnt_incr_1d";
        $date = date("Y-m-d", strtotime("-1 day"));
        $lastId = 0;
        $pageSize = 1000;
        $isEnd = false;
        $list = [];

        do {
            $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_LIST, [
                "table" => strval($table),
                "lastId" => intval($lastId),
                "where" => ["dcxxx" => "1"],
                "date" => strval($date),
                "pageSize" => intval($pageSize),
            ]);
            if (isset($result["data"]) && !empty($result["data"])) {
                $resData = json_decode($result["data"], true);
                foreach ($resData as $value) {
                    $list[$value["sec_category_id"]] = ["cnt" => $value["user_cnt"]];
                }
            }
            if (!isset($result["isEnd"]) || $result["isEnd"] == 1) {
                break;
            }
            $lastId = $result["lastId"];
        } while (!$isEnd);
        return $list;
    }


    // 大客户CRM 用户近90天推荐类目
    public function customerSecCategory(int $userId, int $retries = 1)
    {
        $name = "";
        $table = "api_big_customer_sec_category_incr_1d";
        $date = ""; //最新有数据一天
        $list = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_LIST, [
            "table" => $table,
            "lastId" => 0,
            "where" => ["userinfo_id = ?" => strval($userId)],
            "date" => $date,
            "pageSize" => intval(1)
        ]);
        if (isset($list["data"])) {
            $ilist = json_decode($list["data"], true);
            $name = $ilist[0]["seccategory_name"];
        }
        return $name;
    }

    // 大客户CRM 当日支付金额最高的用户
    public function customerPay($retries = 1)
    {
        $result = ["userinfo_id" => 0, "money" => 0];
        $table = "api_big_customer_pay_incr_1d";
        $date = ""; //最新有数据一天
        $list = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_LIST, [
            "table" => $table,
            "lastId" => 0,
            "where" => ["dcxxx" => "1"],
            "dt" => $date,
            "pageSize" => intval(100)
        ]);
        if (isset($list["data"])) {
            $ilist = json_decode($list["data"], true);
            foreach ($ilist as $value) {
                if (isset($value["money"]) && $value["money"] > $result["money"]) {
                    $result["userinfo_id"] = $value["userinfo_id"];
                    $result["money"] = $value["money"];
                }
            }
        }
        return $result;
    }

    // 大客户CRM 当日实际支付金额最高的用户
    public function customerRealPay($retries = 1)
    {
        $result = [];
        $table = "api_maxmoneymvp_day_incr_1d";
        $date = "";  // 最新有数据一天
        $list = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_LIST, [
            "table" => $table,
            "lastId" => 0,
            "where" => ["dcxxx" => "1"],
            "dt" => $date,
            "pageSize" => intval(100)
        ]);
        if (isset($list["data"])) {
            $result = json_decode($list["data"], true);
        }
        return $result;
    }

    // 大客户CRM growing数据
    public function customerUser($userinfoId, $retries = 1)
    {
        $result = [];
        $table = "api_user_growing_vipcrm_detail_full_1d";
        $date = "";  // 最新有数据一天
        $list = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_LIST, [
            "table" => $table,
            "lastId" => 0,
            "where" => ["userinfo_id = ? " => strval($userinfoId)],
            "dt" => $date,
            "pageSize" => intval(10)
        ]);
        if (isset($list["data"])) {
            $result = json_decode($list["data"], true);
        }
        return $result;
    }

    // 优店备选商家
    public function goodShopCandidate(int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setTraceLimit($retries)->go(Route::BIG_DATA_GOOD_SHOP_CANDIDTAE, ["dcxxx" => "1"]);
        $data = $result["data"] ?? "";
        return !empty($data) ? json_decode($data, true) : [];
    }


    //  店铺实时粉丝
    public function todayFans(int $shopId, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setTraceLimit($retries)->go(
            Route::BIG_DATA_TODAY_FANS,
            ["shopId" => intval($shopId)]
        );
        return $result["data"] ?? [];
    }


    // 是否被刷单
    public function isScalpSale(int $saleId, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_IS_SCALP, [
            "saleId" => intval($saleId)
        ]);
        return $result["data"] ?? [];
    }

    // 全站认证店铺（优店判断）
    public function goodShopVerify(int $shopId, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_GOOD_SHOP_VERFIY, [
            "shopId" => intval($shopId)
        ]);
        $data = $result["data"] ?? "";
        return !empty($data) ? json_decode($data, true) : [];
    }

    // 新商家成长
    public function newSellerGrow(int $shopId, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_NEW_SELLER_GROW, [
            "shopId" => intval($shopId)
        ]);
        $data = $result["data"] ?? "";
        return !empty($data) ? json_decode($data, true) : [];
    }

    // 各二级分类近30天GMV排行商家 TOP300表
    public function secCategoryShopGmvSort(int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_SEC_CATEGORY_GMV_SORT, [
            "dcxxx" => "1"
        ]);
        $data = $result["data"] ?? "";
        $result = !empty($data) ? json_decode($data, true) : [];
        return $result;
    }

    // 店铺过去一个半月历史粉丝
    public function historyFans(int $shopId, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_HISTORY_FANS, [
            "shopId" => intval($shopId),
            "dcxxx" => "1"
        ]);
        $data = $result["data"] ?? "";
        return !empty($data) ? json_decode($data, true) : [];
    }

    /**
     * @param int $shopId
     * @param string $type (deal, paid, refund, finished)
     * @param string $period (lastWeek,lastMonth,month,last365,last30)
     * @param int $retries
     */
    public function shopCapitailDaily(int $shopId, string $type, string $period, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_SHOP_CAPITAIL_DAISY, [
            "shopId" => intval($shopId),
            "typ" => strval($type),
            "period" => strval($period)
        ]);
        $data = $result["data"] ?? "";
        return !empty($data) ? json_decode($data, true) : [];
    }

    public function shopPublishDaily(int $shopId, string $period, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_SHOP_PUBLISH_DAILY, [
            "shopId" => intval($shopId),
            "period" => strval($period)
        ]);
        $data = $result["data"] ?? "";
        $list = !empty($data) ? json_decode($data, true) : [];
        $res = [];
        foreach ($list as $value) {
            $res[$value["dt"]] = $value["num"];
        }
        return $res;
    }

    public function shopCustomerDaily(int $shopId, string $period, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_SHOP_CUSTOMER_DAILY, [
            "shopId" => intval($shopId),
            "period" => strval($period)
        ]);
        $data = $result["data"] ?? "";
        return !empty($data) ? json_decode($data, true) : [];
    }

    public function shopVisitDaily(int $shopId, string $period, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_SHOP_VISIT_DAILY, [
            "shopId" => intval($shopId),
            "period" => strval($period)
        ]);
        $data = $result["data"] ?? "";
        return !empty($data) ? json_decode($data, true) : [];
    }

    public function shopInvitationRankLastWeek(int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_SHOP_INVITATION_RANKE_LAST_WEEK, ["dcxxx" => "1"]);
        $data = $result["data"] ?? "";
        return !empty($data) ? json_decode($data, true) : [];
    }

    public function shopInvitationRank(int $shopId, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_SHOP_INVITATION_RANK, [
            "shopId" => intval($shopId),
        ]);
        $data = $result["data"] ?? "";
        return !empty($data) ? json_decode($data, true) : [];
    }

    public function sellerServiceRate(int $shopId, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_SELLER_SERVICE_RATE, [
            "shopId" => intval($shopId),
        ]);
        $data = $result["data"] ?? "";
        return !empty($data) ? json_decode($data, true) : [];
    }

    public function sellerServiceRateList(int $shopId, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_SELLER_SERVICE_RATE_LIST, [
            "shopId" => intval($shopId)
        ]);
        $data = $result["data"] ?? "";
        return !empty($data) ? json_decode($data, true) : [];
    }

    public function userDealFail(int $pos, int $size, int $retries = 1)
    {

        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_USER_DEAL_FAIL, [
            "pos" => intval($pos),
            "pageSize" => intval($size)
        ]);
        $data = $result["data"] ?? [];
        $details = $data["details"] ?? "";
        $nextPos = $data["pos"] ?? 0;
        $userDealData = !empty($details) ? json_decode($details, true) : [];

        $t = strtotime('-1 day'); // 前一天开始
        $end = date('Y-m-d', $t);
        $start = date('Y-m-d', strtotime('-14 day', $t)); // 从昨天开始连续15天，包括昨天，所以这里用14
        $startTimestamp = strtotime($start);
        $endTimestamp = strtotime($end);
        $dates = [];
        while ($startTimestamp <= $endTimestamp) {
            $dates[] = date('Y-m-d', $startTimestamp);
            $startTimestamp = strtotime("+1 day", $startTimestamp);
        }


        $detailList = [];
        $detailRestult = [];
        $dates = array_reverse($dates); // 日期倒排
        foreach ($userDealData as $uid => $value) {
            foreach ($dates as $dt) {
                if (isset($value[$dt])) {
                    $detailList[$uid][$dt] = $value[$dt]["deal_cnt"] . "_" . $value[$dt]["fail_cnt"];
                } else {
                    $detailList[$uid][$dt] = "0_0";
                }
            }
            $detailRestult[$uid] = implode(',', $detailList[$uid]);
        }

        return ["details" => $detailRestult, "pos" => $nextPos];
    }

    public function IsBidOrShareLive(int $shopId, array $uids, int $retries = 1)
    {
        $newIds = [];
        foreach ($uids as $uid) {
            $newIds[] = intval($uid);
        }
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_IS_BID_SHARE_LIVE, [
            "shopId" => intval($shopId),
            "userIds" => $newIds,
        ]);
        $data = $result["data"] ?? "";
        return !empty($data) ? json_decode($data, true) : [];
    }


    public function liveDaily(int $liveId, string $startDate, string $endDate, int $retries = 1)
    {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_SHOP_LIVE_DAILY, [
            "liveId" => intval($liveId),
            "startDate" => $startDate,
            "endDate" => $endDate,
        ]);
        $data = $result["data"] ?? "";
        return !empty($data) ? json_decode($data, true) : [];
    }

    public function oriCertCnt(
        int $orgId,
        string $startDate,
        string $endDate,
        int $lastId,
        int $siteId = 0,
        int $pageSize = 1000,
        int $retries = 1
    ) {
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_ORG_CERT_CNT, [
            "orgId" => intval($orgId),
            "startDate" => strval($startDate),
            "endDate" => strval($endDate),
            "lastId" => intval($lastId),
            "pageSize" => intval($pageSize),
            "siteId" => intval($siteId)
        ]);
        $total = 0;
        $list = [];
        $nextStartId = $lastId;
        if (isset($result["data"])) {
            $total = $result["data"]["total"] ?? 0;
            $list = !empty($result["data"]["list"]) ? json_decode($result["data"]["list"]) : [];
            $nextStartId = $result["data"]["lastId"] ?? $lastId;
        }
        return ["total" => $total, "list" => $list, "lastId" => $nextStartId];
    }

    public function shopCheckIndexData(int $shopId, int $retries = 1)
    {
        $res = ["today" => [], "yesterday" => []];
        $table = "api_seller_check_up_full_1d";
        $where = ["shop_id = ?" => strval($shopId)];
        $newDate = date("Y-m-d", strtotime("-1 day"));
        // 取最新一天的数据
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_LIST, [
            "table" => strval($table),
            "where" => $where,
            "date" => $newDate,
            "pageSize" => intval(1),
        ]);
        if (isset($result["data"]) && !empty($result["data"])) {
            $list = json_decode($result["data"], true);
            $res["today"] = $list[0];
        } else {
            $newDate = date("Y-m-d", strtotime("-2 day"));
            $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_LIST, [
                "table" => strval($table),
                "where" => $where,
                "date" => $newDate,
                "pageSize" => intval(1),
            ]);
            if (isset($result["data"]) && !empty($result["data"])) {
                $list = json_decode($result["data"], true);
                $res["today"] = $list[0];
            }
        }
        //  取前一天的数据
        if (!empty($res["today"])) {
            $yesterday = date("Y-m-d", strtotime($newDate) - 24 * 3600);
            $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_LIST, [
                "table" => strval($table),
                "where" => $where,
                "date" => $yesterday,
                "pageSize" => intval(1),
            ]);
            if (isset($result["data"]) && !empty($result["data"])) {
                $list = json_decode($result["data"], true);
                $res["yesterday"] = $list[0];
            }
        }
        return $res;
    }

    /**
     * 直播间转化率
     * @param array $shopIds
     * @param string $date
     * @param int $retries
     * @return array
     */
    public function getLiveRoomConvRate(array $shopIds, string $date = "", int $retries = 1)
    {
        $len = count($shopIds);
        if ($len > 100) {
            return [];
        }
        $ids = implode(",", $shopIds);
        $where = ['shop_id IN (?)' => $ids];
        $result = $this->getBigDataList("api_liveroom_conv_incr_1d", 0, $where, $len, $date, $retries);
        return $result['data'] ?? [];
    }

    /**
     * 过去7天中奖次数
     * @param array $uid
     * @param string $date
     * @param int $retries
     * @return array|mixed
     */
    public function last7DayWinningTimes(array $uid, string $date = "", int $retries = 1)
    {
        $len = count($uid);
        if ($len > 100) {
            return [];
        }

        $ids = implode(",", $uid);
        $where = ['userinfo_id IN (?)' => $ids];
        $result = $this->getBigDataList("api_live_raise_win_shopcnt_full_1d", 0, $where, $len, $date, $retries);
        $data = $result['data'] ?? [];
        if (empty($data)) {
            return [];
        }

        $result = [];
        foreach ($data as $k => $v) {
            $result[$v['userinfo_id']] = $v['shop_cnt'];
        }

        return $result;
    }


    /**
     * 之前参与过抽奖，但7天内未参与
     * @param $lastId
     * @param int $pageSize
     * @return array
     * @throws \Exception
     */
    public function get7DayAttendRaiseNotEnterLiveRoom($lastId, $pageSize = 1000)
    {
        $result = $this->getBigDataList("api_live_raise_msgpush_user_full_1d", $lastId, ['type = ?' => 1], $pageSize);
        $isEnd = $result['isEnd'] ?? true;
        $lastId = $result['lastId'] ?? 0;
        $data = $result['data'] ?? [];
        if (empty($data)) {
            return [
                'isEnd' => true,
                'lastId' => $lastId,
                'data' => [],
            ];
        }
        return [
            'isEnd' => $isEnd,
            'lastId' => $lastId,
            'data' => array_column($data, 'userinfo_id'),
        ];
    }

    /**
     * 未参与过但近7天进入过直播间
     * @param $lastId
     * @param int $pageSize
     * @return array
     * @throws \Exception
     */
    public function get7DayNotAttendRaiseEnterLiveRoom($lastId, $pageSize = 1000)
    {
        $result = $this->getBigDataList("api_live_raise_msgpush_user_full_1d", $lastId, ['type = ?' => 2], $pageSize);
        $isEnd = $result['isEnd'] ?? true;
        $lastId = $result['lastId'] ?? 0;
        $data = $result['data'] ?? [];
        if (empty($data)) {
            return [
                'isEnd' => true,
                'lastId' => $lastId,
                'data' => [],
            ];
        }
        return [
            'isEnd' => $isEnd,
            'lastId' => $lastId,
            'data' => array_column($data, 'userinfo_id'),
        ];
    }


    /**
     * 流量翻倍实时带来访问和成交人数
     * @param int $shopId
     * @param string $date
     * @param int $retries
     * @return array
     */
    public function doubleFlowRealtimeDealAndVisit(int $shopId, string $date, int $retries = 1)
    {
        $result = ["deal" => 0, "visit" => 0, "date" => ""];
        // 成交人数
        $dealData = $this->getBigDataList(
            "api_double_flow_bring_deal_realtime_incr_1d", 0,
            ['shop_id = ?' => intval($shopId)], 1, $date, $retries);
        $data = $dealData['data'] ?? [];
        if (!empty($data)) {
            $result["deal"] = $data[0]["userinfo_count"];
        }
        // 访问人数
        $visitData = $this->getBigDataList(
            "api_double_flow_bring_visit_realtime_incr_1d", 0,
            ['shop_id = ?' => intval($shopId)], 1, $date, $retries);
        $data = $visitData['data'] ?? [];
        if (!empty($data)) {
            $result["visit"] = $data[0]["userinfo_count"];
        }
        return $result;
    }

    /**
     * 非0元支付的订单笔数大于3的用户
     * @param array $userIds
     * @param int $retries
     * @return array
     */
    public function noZeroPayUser(array $userIds, int $retries = 1)
    {

        if (count($userIds) > 100) {
            throw new DataCenterException("获取非零元支付超过3笔的用户时 userIds 超过限制100");
        }
        $table = "api_user_nozero_pay_incr_1d";
        $pageSize = count($userIds);
        $where = ["userinfo_id IN (?)" => implode(",", $userIds)];
        $list = DataCenter::getInstance()->getBigDataList($table, 0, $where, $pageSize);
        $data = $list["data"] ?? [];
        $result = array_column($data, "userinfo_id");
        return $result;
    }

    /**
     * 直播消息推送
     * @param int $type
     * @param int $lastId
     * @param int $pageSize
     * @return array
     * @author wangwenbo
     */
    public function livePush($type, $lastId = 0, $pageSize = 3000)
    {
        $result = ["lastId" => $lastId, "isEnd" => true, "data" => []];
        $table = "api_live_push_full_1d";
        $where = ["push_type = ?" => strval($type)];
        $date = date("Y-m-d", strtotime("-1 day"));

        $list = DataCenter::getInstance()->getBigDataList($table, intval($lastId), $where, intval($pageSize), $date);

        $data = $list["data"] ?? [];
        $result["data"] = array_column($data, "userinfo_id");
        $result["lastId"] = $list["lastId"] ?? $lastId;
        $result["isEnd"] = $list["isEnd"] ?? true;
        return $result;
    }


    public function relationUser($userId, $type = "", $retries = 1)
    {
        $data = [];
        $result = CurlHandler::getInstance()->setRetries($retries)->go(Route::BIG_DATA_RELATION_UER, [
            "userId" => intval($userId),
            "type" => strval($type),
        ]);
        if (isset($result["data"]) && !empty($result["data"])) {
            $data = json_decode($result["data"], true);
        }
        return $data;
    }
}







