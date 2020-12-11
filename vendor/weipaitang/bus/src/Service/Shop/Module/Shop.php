<?php


namespace WptBus\Service\Shop\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Shop\Router;

class Shop extends BaseService
{
    /**
     * 店铺基础信息
     * @param string $identity  uri/shopId
     * @param array $fields     要查询的字段（支持字段如下）
     * [
     * "shopId",            shopId
     * "shopUri",           shopUri
     * "shopName",          店铺名称
     * "shopLogo",          店铺logo
     * "shopIntroduce",     店铺介绍
     * "isVerified",        是否认证中（根据认证过期时间大于当前时间判断所得）
     * "verifyType",        认证类型 individual个人认证，business企业认证
     * "verifiedTime",      认证时间
     * "expiredTime",       认证过期时间
     * "rate",              店铺评分
     * "sellerLevel",       卖家等级
     * "sellerLevelScores", 卖家等级积分
     * "isGoodShop",        是否优店 (t_shop表goodShopable==1)
     * "goodshopable",      优店信息 0否 1优店 2下优店
     * "isRecommend",       是否精选（判断精选开始时间<=现在 && 结束时间>=现在）
     * "sparkLevel",        当前星火等级（星火等级值&&星火等级时间==今天或者昨天 否则为0）
     * "firstVerifyTime",   首次认证时间 由于历史脏数据问题可能不准 业务上斟酌使用
     * "isAuction",         是否拍卖行  （根据认证类型=企业 && 认证过期时间>当前 && t_shop表isAuction=1）
     * "isFood"             是否食品许可（根据认证类型=企业 && 认证过期时间>当前 && t_shop表isFood=1）
     * "enterpriseType",    business企业认证再分类："0" 未申请，"1"普通企业 "2"个体工商户，目前大部分场景使用要结合认证类型==企业 && 认证未过期
     * "scopedCategories"   授权可以发布拍品分类
     * "isBrand"            是否品牌馆 true是 false否
     * ]
     *
     * @return array|void 返回数据示例如下
     *
     * {
     * "enterpriseType": 1,
     * "expiredTime": 1627725652,
     * "firstVerifyTime": 1596967252,
     * "goodshopable": 2,
     * "isAuction": true,
     * "isFood": false,
     * "isGoodShop": false,
     * "isRecommend": false,
     * "isVerified": true,
     * "rate": "3.62",
     * "scopedCategories": "3001,5024",
     * "sellerLevel": 3,
     * "sellerLevelScores": 201,
     * "shopId": 8615961,
     * "shopIntroduce": "介绍啊",
     * "shopLogo": "https://cdn01t.weipaitang.com/certify/20190307cleotdzq-pt5x-nmmo-koet-fbtsoaycmodc-W1000H1000/w/0",
     * "shopName": "3b",
     * "shopUri": "1903071321PkRwtt",
     * "sparkLevel": 2,
     * "verifiedTime": 1591097059,
     * "isBrand": true,
     * "verifyType": "business"
     * }
     */
    public function getInfo(string $identity, array $fields)
    {
        $data = ["identity" => $identity, "fields" => $fields];
        if ($error = $this->validate($data, ["identity" => "required|string", "fields" => "required|array"])) {
            return $error;
        }
        $this->setTimeout(1500)->setRetryTimes(1);
        $ret = $this->httpPost(Router::GET_INFO, $data);
        $this->dealResultData($ret, function ($data) {
            return $data ? json_decode($data, true) : [];
        });
        return $ret;
    }


    /**
     * 批量获取店铺信息，请求和返回同上，一次最多20个
     * @param array $identityList
     * @param array $fields
     * @return array|void 返回数据示例如下
     *
     * {
     * "8615961": {
     * "rate": "5.00",
     * "sellerLevel": 7,
     * "sellerLevelScores": 20008,
     * "shopId": 8615961,
     * "shopUri": "1903071321PkRwtt"
     * },
     * "53": {
     * "rate": "5.00",
     * "sellerLevel": 3,
     * "sellerLevelScores": 201,
     * "shopId": 53,
     * "shopUri": "1706081224p5jUZc"
     * }
     * }
     *
     */
    public function batchGetShopInfo(array $identityList, array $fields)
    {
        foreach ($identityList as &$item) {
            $item = (string)$item;
        }
        $data = ["identityList" => $identityList, "fields" => $fields];
        if ($error = $this->validate($data, ["identityList" => "required|array", "fields" => "required|array"])) {
            return $error;
        }
        $this->setTimeout(1500)->setRetryTimes(1);
        $ret = $this->httpPost(Router::BATCH_GET_SHOP_INFO, $data);
        $this->dealResultData($ret, function ($data) {
            return $data ? json_decode($data, true) : [];
        });
        return $ret;
    }

    /**
     * 更新卖家积分等级
     * @param int $shopId
     * @param int $scores
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604468220,
     * "data": [],
     * }
     */
    public function updateSellerLevelScores(int $shopId,int $scores)
    {
        return $this->httpPost(\WptBus\Service\User\Router::UPDATE_SELLER_LEVEL_SCORES, [
            "uid" => $shopId,
            "scores" => $scores,
        ]);
    }


    /**
     * 更新品牌馆信息
     * @param int $shopId
     * @param int $isBrand 1是 0否
     * @return array|void
     *
     * 返回数据示例：
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604382604
     * }
     *
     */
    public function updateIsBrand(int $shopId, int $isBrand)
    {
        $data = ["shopId" => $shopId, "isBrand" => $isBrand];
        if ($error = $this->validate($data, ["shopId" => "required|int", "isBrand" => "required|int"])) {
            return $error;
        }

        return $this->httpPost(Router::UPDATE_IS_BRAND, $data);
    }


    /**
     * 业务鉴权（迁移user-service sdk）
     * @param $shopId
     * @param $businessType
     * @return array
     */
    public function auth($shopId, $businessType)
    {
        $params = ["uid" => (int)$shopId, "business" => (string)$businessType];

        return $this->httpPost(Router::BUSINESS_AUTH, $params);
    }

    const SHOP_PART_BASE = 1; //店铺基本信息(头像、昵称、等级、地区、城市、店铺简介、个性签名）
    const SHOP_PART_DOOR_HEAD = 2;  //店铺门头(精选、优店、个人认证、企业认证、上市公司、食品许可)
    const SHOP_PART_RATE_LIST = 3;       //店铺评价
    const SHOP_PART_DATA_STATISTICS = 4; //店铺的评分、违约、争议等统计
    const SHOP_PART_BID_RULE = 5; //店铺出价规则

    /**
     * APP端 查询店铺(基础信息、门头、统计指标和用户评价) （迁移user-service sdk）
     * @param string $shopUri
     * @param bool $isSelf
     * @param bool $isGetFans
     * @return array
     */
    public function inquireShop(string $shopUri, bool $isSelf = false, bool $isGetFans = false)
    {
        $parts = [self::SHOP_PART_BASE, self::SHOP_PART_DOOR_HEAD, self::SHOP_PART_DATA_STATISTICS, self::SHOP_PART_RATE_LIST];
        $params = ["uri" => $shopUri, "parts" => $parts, "isSelf" => $isSelf, "isGetfans" => $isGetFans];

        return $this->httpPost(Router::INQUIRE_SHOP_BY_PARTS, $params);
    }


    /**
     * APP端 查询店铺（指定返回部分属性数据：基础信息、门头、统计指标和用户评价,出价规则等）
     * @param string $shopUri
     * @param array $parts
     * @param bool $isSelf
     * @param bool $isGetFans
     * @return array
     */
    public function inquireShopByParts(string $shopUri, array $parts, bool $isSelf = false, bool $isGetFans = false)
    {
        $res = [];
        if ($shopUri == "null" || $shopUri == "undefined") {
            return $res;
        }
        $params = ["uri" => $shopUri, "parts" => $parts, "isSelf" => $isSelf, "isGetfans" => $isGetFans];

        return $this->httpPost(Router::INQUIRE_SHOP_BY_PARTS, $params);
    }


    /**
     * APP端 批量查询店铺
     * @param array $userinfoIds
     * @param array $parts
     * @param bool $isSelf
     * @param bool $isGetFans
     * @return array
     */
    public function batchInquireShop(array $userinfoIds, array $parts, bool $isSelf = false, bool $isGetFans = false)
    {
        $uids = array_map("intval", $userinfoIds);
        $params = ["uids" => $uids, "parts" => $parts, "isSelf" => $isSelf, "isGetFans" => $isGetFans];

        return $this->httpPost(Router::BATCH_INQUIRE_SHOP_BY_PARTS, $params);
    }

}