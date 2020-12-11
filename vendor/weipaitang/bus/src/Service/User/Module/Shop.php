<?php


namespace WptBus\Service\User\Module;

use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class Shop extends BaseService
{
    /**
     * 是否是封店店铺
     * @param int $uid
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1585710979,
     * "data": true
     * }
     */
    public function isForbiddenShop(int $uid)
    {
        $params = ["uid" => (int)$uid];
        $ret = $this->httpPost(Router::IS_FORBIDDEN_SHOP, $params);
        return $ret;
    }

    /**
     * 是否是降权18分
     * @param int $uid
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1585710979,
     * "data": true
     * }
     */
    public function isReduce(int $uid)
    {
        $params = ["uid" => (int)$uid];
        $ret = $this->httpPost(Router::IS_REDUCE, $params);
        return $ret;
    }

    /**
     * 是否是禁止发拍
     * @param int $uid
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1585710979,
     * "data": true
     * }
     */
    public function isForbiddenPublish(int $uid)
    {
        $params = ["uid" => (int)$uid];
        $ret = $this->httpPost(Router::IS_FORBIIDEN_PUBLISH, $params);
        return $ret;
    }

    /**
     * 批量查询是否封店
     * @param array $uids [10300004,222222222]
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1585711112,
     * "data": {
     * "10300004": true,
     * "222222222": false
     * }
     * }
     */
    public function inquireBatchForbiddenShop(array $uids)
    {
        $params = ["uids" => array_values($uids)];
        $ret = $this->httpPost(Router::INQUIRE_BATCH_FORBIDDEN_SHOP, $params);
        return $ret;
    }

    /**
     * 批量查询是否降权
     * @param array $uids [10300004,222222222]
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1585711112,
     * "data": {
     * "10300004": true,
     * "222222222": false
     * }
     * }
     */
    public function inquireBatchReduce(array $uids)
    {
        $params = ["uids" => $uids];
        $ret = $this->httpPost(Router::INQUIEE_BATCH_REDUCE, $params);
        return $ret;
    }

    /**
     * 是否是涮单
     * @param int $uid
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1585710979,
     * "data": true
     * }
     */
    public function isScalping(int $uid)
    {
        $params = ["uid" => (int)$uid];
        $ret = $this->httpPost(Router::IS_SCALPING, $params);
        return $ret;
    }

    /**
     * 是否是禁止提现
     * @param int $uid
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1585710979,
     * "data": true
     * }
     */
    public function isForbiddenWithdraw(int $uid)
    {
        $params = ["uid" => (int)$uid];
        $ret = $this->httpPost(Router::IS_FORBIDDEN_WITHDRAW, $params);
        return $ret;
    }

    public function getGoodShopWithoutForbiddenShop()
    {
        $params = ["source" => 2];
        $ret = $this->httpPost(Router::GET_GOODSHOP_WITHOUT_FORBIDDENSHOP, $params);
        return $ret;
    }

    public function addCategoryWeight(int $uid)
    {
        $params = ["userinfoId" => (int)$uid];
        if ($error = $this->validate($params, ['userinfoId' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::ADD_CATEGORY_WEIGHT_LIST, $params);
        return $ret;
    }

    public function delCategoryWeight(int $uid)
    {
        $params = ["userinfoId" => (int)$uid];
        if ($error = $this->validate($params, ['userinfoId' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::DEL_CATEGORY_WEIGHT_LIST, $params);
        return $ret;
    }

    public function getCategoryWeight(int $uid)
    {
        $params = ["userinfoId" => (int)$uid];
        if ($error = $this->validate($params, ['userinfoId' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_ONE_CATEGORY_WEIGHT_LIST, $params);
        return $ret;
    }

    public function getAllCategoryWeightList()
    {
        $params = ["source" => 2];
        $ret = $this->httpPost(Router::GET_ALL_CATEGORY_WEIGHT_LIST, $params);
        return $ret;
    }

    /**
     * 获取设置的部分信息
     * @param $userinfoId
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1585711798,
     * "data": {
     * "enableReturn": 1,
     * "agreeBzj": 1
     * }
     * }
     */
    public function getShopPartPublishSetting($userinfoId)
    {
        $params = ["userinfoId" => (int)$userinfoId];
        if ($error = $this->validate($params, ['userinfoId' => 'required'])) {
            return $error;
        }
        return $this->httpPost(Router::GET_SHOP_PART_PUBLISH_SETTING, $params);
    }

    //判断卖家是否新手
    public function isNoviceSeller($uid)
    {
        $params = ["uid" => (int)$uid];
        if ($error = $this->validate($params, ['uid' => 'required'])) {
            return false;
        }
        $res = $this->httpPost(Router::IS_NOVICE_SELLER, $params);
        if ($res['code'] == 0) {
            return $res['data'];
        }
        return false;
    }

    //发布拍品后更新卖家一些设置信息
    public function updateSellerInfoAfterPublish($userinfoId, $types)
    {
        $params = ["userinfoId" => (int)$userinfoId, "types" => (array)$types];
        if ($error = $this->validate($params, ['userinfoId' => 'required'])) {
            return false;
        }
        $res = $this->httpPost(Router::UPDATE_SELLER_INFO_AFTER_PUBLISH, $params);
        if ($res['code'] == 0) {
            return true;
        }
        return false;
    }

    /**
     * 拍品详情定制商家聚合信息 粉丝数、是否关注、店铺标签(要废弃，新查询请不要使用)
     * @param $userinfoId
     * @param $shopId
     * @param $fields //数组内字段应为字符串
     * @return array
     */
    public function GetShopDetail(int $userinfoId, int $shopId, array $fields)
    {
        if ($shopId <= 0 || $userinfoId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "userinfoId、shopId 必须");
        }
        $fields = array_unique($fields);
        if (count($fields) == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "fields 必须");
        }

        $parame = [
            'uid' => $userinfoId,
            'shopUid' => $shopId,
            'fields' => $fields,
        ];

        $ret = $this->httpPost(Router::GET_SHOP_DETAIL, $parame);

        return $ret;
    }

    /**
     * 商家店铺聚合信息(要废弃，新查询请不要使用)
     * @param $userinfoId
     * @param $shopId
     * @param $fields // 数组内字段应为字符串
     * @return array
     */
    public function GetShopInfo(int $userinfoId, int $shopId, array $fields)
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 必须");
        }
        $fields = array_unique($fields);
        if (count($fields) == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "fields 必须");
        }
        if (in_array("isBeBlack", $fields) && $userinfoId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "userinfoId 必须");
        }
        if (in_array("isAttention", $fields) && $userinfoId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "userinfoId 必须");
        }

        $parame = [
            'userinfoId' => $userinfoId,
            'shopId' => $shopId,
            'fields' => $fields,
        ];


        $ret = $this->httpPost(Router::GET_SHOP_INFO, $parame);

        return $ret;
    }

    /**
     * 商家今日服务报表信息
     * @param $shopId
     * @return array
     */
    public function GetShopTodayServingReport(int $shopId)
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 必须");
        }
        $parame = [
            'shopId' => $shopId,
        ];

        $ret = $this->httpPost(Router::GET_SHOP_TODAY_SERVING_REPORT, $parame);

        return $ret;
    }

    /**
     * 商家每天服务报表信息
     * @param $shopId
     * @return array
     */
    public function GetShopDailyServingReport(int $shopId)
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 必须");
        }
        $parame = [
            'shopId' => $shopId,
        ];

        $ret = $this->httpPost(Router::GET_SHOP_DAILY_SERVING_REPORT, $parame);

        return $ret;
    }

    /**
     * 同步店铺信息的精选相关时间
     * @param $shopId
     * @param $recommendStartTime
     * @param $recommendEndTime
     * @return array
     */
    public function UpdateShopRecommendStartAndEndTime(int $shopId, int $recommendStartTime, int $recommendEndTime)
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 必须");
        }

        if ($recommendStartTime <= 0 || $recommendEndTime <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "recommendStartTime or recommendEndTime 错误");
        }

        $parame = [
            'shopId' => $shopId,
            'recommendStartTime' => $recommendStartTime,
            'recommendEndTime' => $recommendEndTime,
        ];

        $ret = $this->httpPost(Router::UPDATE_SHOP_RecommendStartAndEndTime, $parame);

        return $ret;
    }

    /**
     * 同步店铺信息的enterpriseType
     * @param $shopId
     * @param $enterpriseType
     * @return array
     */
    public function UpdateShopEnterpriseType(int $shopId, int $enterpriseType)
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 必须");
        }

        if ($enterpriseType < 0 || $enterpriseType > 2) {
            return Response::byBus(Error::INVALID_ARGUMENT, "enterpriseType 错误");
        }

        $parame = [
            'shopId' => $shopId,
            'enterpriseType' => $enterpriseType,
        ];

        $ret = $this->httpPost(Router::UPDATE_SHOP_EnterpriseType, $parame);

        return $ret;
    }

    /**
     * 同步店铺信息的goodshopable (上下优店)
     * @param $shopId
     * @param $goodshopable
     * @return array
     */
    public function UpdateShopGoodshopable(int $shopId, int $goodshopable)
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 必须");
        }

        if ($goodshopable < 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "goodshopable 错误");
        }

        $parame = [
            'shopId' => $shopId,
            'goodshopable' => $goodshopable,
        ];

        $ret = $this->httpPost(Router::UPDATE_SHOP_Goodshopable, $parame);

        return $ret;
    }

    /**
     * 同步店铺信息的sellerLevelScores (卖家等级积分)
     * @param $shopId
     * @param $sellerLevelScores
     * @return array
     */
    public function UpdateShopSellerLevelScores(int $shopId, int $sellerLevelScores)
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 必须");
        }

        $parame = [
            'shopId' => $shopId,
            'sellerLevelScores' => $sellerLevelScores,
        ];

        $ret = $this->httpPost(Router::UPDATE_SHOP_SellerLevelScores, $parame);

        return $ret;
    }

    /**
     * 获取新店铺信息（wpt_shop库的t_shop表信息）
     * @param $shopId
     * @param $fields
     * @return array
     */
    public function GetTShopInfo(int $shopId, array $fields)
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 必须");
        }
        if (count($fields) <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "fields 必须");
        }

        $parame = [
            'shopId' => $shopId,
            'fields' => $fields,
        ];

        $ret = $this->httpPost(Router::Get_T_Shop_Info, $parame);

        return $ret;
    }


    /**
     * 分页获取t_shop表数据
     * @param array $columns
     * @param string $queryWhere
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function GetShopInfoList(array $columns, string $queryWhere, $order = "", $limit = 50, $offset = 0)
    {
        if (count($columns) == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "columns 必须");
        }
        $parame = [
            'columns' => $columns,
            'queryWhere' => $queryWhere,
            'order' => $order,
            'limit' => $limit,
            'offset' => $offset,
        ];

        $ret = $this->httpPost(Router::GET_SHOP_INFO_LIST, $parame);

        return $ret;
    }


    /**
     * 店铺装修 保存首页背景、公告、简介信息
     * @param int $shopId   店铺ID
     * @param array $fields 要更新的基础数据  类型应为 map[string]string类型（数组中的值类型也应为字符串）
     * @param array $plugs  更新公告时候用到  表示要更新的组件化信息
     * @return array
     */
    public function SaveShopCustomPageInfo(int $shopId, array $fields, array $plugs = [])
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 应大于0");
        }
        if (count($fields) == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "fields 必须");
        }
        $parame = [
            'shopId' => $shopId,
            'fields' => $fields,
            'plugs' => array_values($plugs),
        ];

        $ret = $this->httpPost(Router::SAVE_SHOP_CUSTOM_PAGE_INFO, $parame);

        return $ret;
    }


    /**
     * 店铺装修 删除首页背景、公告、简介信息
     * @param int $shopId    店铺ID
     * @param int $useScene  场景信息 0普通 1首页 2公告 3简介
     * @return array
     */
    public function DelShopCustomPageInfo(int $shopId, int $useScene)
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 应大于0");
        }
        $parame = [
            'shopId' => $shopId,
            'useScene' => $useScene,
        ];

        $ret = $this->httpPost(Router::DEL_SHOP_CUSTOM_PAGE_INFO, $parame);

        return $ret;
    }


    /**
     * 获取首页背景
     * @param int $shopId      店铺ID
     * @param int $useScene    场景信息 0普通 1首页 2公告 3简介
     * @param int $pageId      微页面ID
     * @param string $shopUri  店铺uri
     * @param int $review      是否预览 0否 1是
     * @return array
     */
    public function GetShopCustomPageInfo(int $shopId, int $useScene, int $pageId, string $shopUri, int $review)
    {
        $parame = [
            'shopId' => $shopId,
            'useScene' => $useScene,
            'pageId' => $pageId,
            'shopUri' => $shopUri,
            'review' => $review,
        ];

        $ret = $this->httpPost(Router::GET_SHOP_CUSTOM_PAGE_INFO, $parame);

        return $ret;
    }

    /**
     * 根据店铺ID获取包括首页背景、公告、简介三种信息
     * @param int $shopId
     * @return array
     */
    public function GetShopDecorationByShopId(int $shopId) {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 应大于0");
        }

        $parame = [
            'shopId' => $shopId,
        ];
        $ret = $this->httpPost(Router::GET_SHOP_DECORATION_BY_SHOP_ID, $parame);

        return $ret;
    }


    /**
     * 店铺设置
     * @param int $shopId   用户店铺ID
     * @param array $fields 要更新的数据 注意fields应为map[string]string类型
     * [
     * "shopRuleJson"=>"{"autoPayBzj":false,"likeHidden":false,"bidbzj":0,"bidbzjLimit":1}",
     * "updateTime" => "1596182459"
     * ]
     * @return array
     */
    public function setShopRule(int $shopId, array $fields) {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 应大于0");
        }
        if (count($fields) == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "fields 必须");
        }

        $parame = [
            'uid' => $shopId,
            'fields' => $fields,
        ];
        $ret = $this->httpPost(Router::SET_SHOP_RULE, $parame);

        return $ret;
    }


    /**
     * 获取店铺设置信息
     * @param int $shopId    用户店铺ID
     * @param array $fields  要查询的字段
     * [
     * "id", "userId", "shopRuleJson", "createTime", "updateTime", "modifyTime"
     * ]
     * @return array
     */
    public function getShopRule(int $shopId, array $fields) {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 应大于0");
        }
        if (count($fields) == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "fields 必须");
        }

        $parame = [
            'uid' => $shopId,
            'fields' => $fields,
        ];
        $ret = $this->httpPost(Router::GET_SHOP_RULE_INFO, $parame);

        return $ret;
    }
}
