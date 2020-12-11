<?php


namespace WptBus\Service\User\Module;

use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class Friend extends BaseService
{

    /**
     * 关注列表
     * @param int $uid
     * @param string $score
     * @param int $limit
     * @param array $fields
     * @return array
     */
    public function GetFriendAttentionList(int $uid, string $score, int $limit, array $fields = [])
    {
        if ($uid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid 必须");
        }
        $parame = [
            'uid' => $uid,
            'score' => $score,
            'limit' => $limit,
            'fields' => $fields,
        ];

        $ret = $this->httpPost(Router::GET_FRIEND_ATTENTION_LIST, $parame);

        return $ret;
    }

    /**
     * 粉丝数
     * @param int $shopId
     * @return array
     */
    public function GetFanNums(int $shopId)
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 必须");
        }
        $parame = [
            'shopId' => $shopId,
        ];

        $ret = $this->httpPost(Router::GET_FAN_NUMS, $parame);

        return $ret;
    }

    /**
     * 置顶/取消置顶
     * @param int $uid 登陆用户uid
     * @param int $attentionUid 关注的用户uid
     * @param bool $isTop 是否置顶 true是 false否
     * @return array
     */
    public function topAttentionInfo(int $uid, int $attentionUid, bool $isTop)
    {
        if ($uid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid错误，应大于0");
        }
        if ($attentionUid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "attentionUid错误，应大于0");
        }
        $parame = [
            'uid' => $uid,
            'attentionUid' => $attentionUid,
            'isTop' => $isTop,
        ];

        return $this->httpPost(Router::TOP_ATTENTION_INFO, $parame);
    }


    /**
     * 获取用户关系 （迁移user-service sdk）
     * @param int $uid
     * @param int $relUid
     * @param int $type [1 => 'paytimes 支付次数', 2 => 'penaltyscore 违约分']
     * @return int|null
     */
    public function getUserRelation(int $uid, int $relUid, int $type)
    {
        if (!in_array($type, [1, 2])) {
            return null;
        }

        $params = [
            'uid' => $uid,
            'relUid' => $relUid,
            'type' => $type,
        ];

        $res = $this->httpPost(Router::GET_USER_RELATION, $params);
        if (!isset($res['code']) || $res['code'] >= 200000) {
            return null;
        }
        return !empty($res["data"]) ? (int)$res["data"] : null;
    }


    /**
     * 增加支付次数或者违约分（迁移user-service sdk）
     * @param int $uid
     * @param int $relUid
     * @param int $type
     * @param int $incr
     * @return bool
     */
    public function updateRelation(int $uid, int $relUid, int $type, int $incr)
    {
        if (!in_array($type, [1, 2])) {
            return false;
        }

        $params = [
            'uid' => $uid,
            'relUid' => $relUid,
            'type' => $type,
            'incr' => $incr,
        ];

        $res = $this->httpPost(Router::UPDATE_RELATION, $params);
        if (!isset($res['code']) || $res['code'] >= 200000) {
            return false;
        }
        return !empty($res["data"]) ? (bool)$res["data"] : false;
    }


    /**
     * 根据个人身份证查询个人关联和认证关联（迁移user-service sdk）
     * @param int $uid
     * @param int $type
     * @return array
     */
    public function getUserRelationByIdCode(int $uid, int $type)
    {
        if ($uid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid不能为空");
        }
        if ($type != 1 && $type != 2) {
            return Response::byBus(Error::INVALID_ARGUMENT, "type错误");
        }

        $params = [
            'uid' => (int)$uid,
            'type' => (int)$type,
        ];
        $res = $this->httpPost(Router::GET_USER_RELATION_BY_ID_CODE, $params);
        if (!isset($res['code'])) {
            return Response::byBus(Error::INVALID_ARGUMENT, "curl 请求错误");
        }

        // 默认值
        $result['data'] = [
            "relationContent" => "0",
            "indivRelationIds" => [],
            "verifyRelationIds" => [],
        ];
        if ($res['code'] == 202078) {
            return $result;
        }
        if ($res['code'] >= 200000) {
            return Response::byBus($res['code'], $res['msg']);
        }

        if (!empty($res['data'])) {
            $result['data'] = (array)$res['data'];
        }

        return $result;
    }

    /**
     * 获取用户关注数量
     * @param int $uid
     * @return array
     * {
     *     "code": 0,
     *     "msg": "",
     *     "nowTime": 1605703409,
     *     "data": 1767
     * }
     */
    public function getAttentionNum(int $uid)
    {
        if ($uid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid 必须");
        }
        $params = [
            'uid' => $uid,
        ];

        return $this->httpPost(Router::GET_ATTENTION_NUM, $params);
    }

    /**
     * 获取关注信息
     * @param int $uid
     * @param int $shopId
     * @return array
     * {
     *     "code": 0,
     *     "msg": "",
     *     "nowTime": 1605703468,
     *     "data": {
     *         "isAttention": 1,
     *         "isDisturb": 1,
     *         "dealNum": 1,
     *         "updateTime": 1605682516
     *     }
     * }
     */
    public function getAttentionInfo(int $uid,int $shopId)
    {
        if ($uid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid 必须");
        }
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 必须");
        }
        $params = [
            'uid' => $uid,
            'shopId' => $shopId,
        ];

        return $this->httpPost(Router::GET_ATTENTION_INFO, $params);
    }

    /**
     * 获取关注信息
     * @param int $uid
     * @param array $shopIds
     * @return array
     * {
     *     "code": 0,
     *     "msg": "",
     *     "nowTime": 1605790276,
     *     "data": {
     *         "10000": {
     *             "isAttention": 1,
     *             "isDisturb": 1,
     *             "dealNum": 4,
     *             "updateTime": 1605750101
     *         }
     *     }
     * }
     */
    public function getAttentionInfoBatch(int $uid,array $shopIds)
    {
        if ($uid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid 必须");
        }
        $params = [
            'uid' => $uid,
            'shopIds' => $shopIds,
        ];

        return $this->httpPost(Router::GET_ATTENTION_INFO_BATCH, $params);
    }
    /**
     * 获取所有的关注店铺Id
     * @param int $uid
     * @return array
     * {
     *     "code": 0,
     *     "msg": "",
     *     "nowTime": 1605703542,
     *     "data": [
     *         8625044,
     *         8620223,
     *         5,
     *         3,
     *         1304084,
     *         2
     *     ]
     * }
     */
    public function getAttentionShopIdAllList(int $uid)
    {
        if ($uid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid 必须");
        }

        $params = [
            'uid' => $uid,
        ];

        return $this->httpPost(Router::GET_ATTENTION_SHOP_ID_ALL_LIST, $params);
    }

    /**
     * 修改关注信息
     * @param int $uid
     * @param int $shopId
     * @param int $isAttention
     * @param string $source
     * @return array
     * {
     *     "code": 0,
     *     "msg": "",
     *     "nowTime": 1605692770,
     *     "data": ""
     * }
     */
    public function updateAttention(int $uid,int $shopId,int $isAttention,string $source)
    {
        if ($uid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid 必须");
        }
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 必须");
        }
        $params = [
            'uid' => $uid,
            'shopId' => $shopId,
            'isAttention' => $isAttention,
            'source' => $source,
        ];

        return $this->httpPost(Router::UPDATE_ATTENTION, $params);
    }

    /**
     * 批量修改关注信息
     * @param array $data
     * [
     *  [
     *  'uid' => $uid,
     *  'shopId' => $shopId,
     *  'isAttention' => $isAttention,
     *  'source' => $source,
     *  ]
     * ]
     * @return array
     * {
     *     "code": 0,
     *     "msg": "",
     *     "nowTime": 1605692920,
     *     "data": ""
     * }
     */
    public function updateAttentionBatch(array $data)
    {
        $params = [
            'data' => $data,
        ];
        return $this->httpPost(Router::UPDATE_ATTENTION_BATCH, $params);
    }

    /**
     * 更新成功交易次数（累加）
     * @param int $uid
     * @param int $shopId
     * @param int $dealNum
     * @return array
     * {
     *     "code": 0,
     *     "msg": "",
     *     "nowTime": 1605692770,
     *     "data": ""
     * }
     */
    public function updateDealNum(int $uid,int $shopId,int $dealNum)
    {
        if ($uid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid 必须");
        }
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 必须");
        }
        $params = [
            'uid' => $uid,
            'shopId' => $shopId,
            'dealNum' => $dealNum,
        ];

        return $this->httpPost(Router::UPDATE_DEAL_NUM, $params);
    }

    /**
     * 获取关注店铺的拍品Id
     * @param int $uid
     * @param int $limit
     * @param string $score
     * @param bool $reverse
     * @return array

     */
    public function getAttentionShopSaleIdList(int $uid,int $limit,string $score,bool $reverse)
    {
        if ($uid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid 必须");
        }
        $params = [
            'uid' => $uid,
            'limit' => $limit,
            'score' => $score,
            'reverse' => $reverse,
        ];

        return $this->httpPost(Router::GET_ATTENTION_SHOP_SALE_ID_LIST, $params);
    }

    /**
     * 获取粉丝列表
     * @param int $uid
     * @param string $score
     * @param int $limit
     * @param bool $assoc
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1606390986,
     * "data": [
     * {
     * "fanUserinfoId": 10500230,
     * "score": "vsfBY7Gt1",
     * "isAttention": -1,
     * "isDisturb": 0,
     * "dealnum": 0,
     * "updateTime": 0
     * },
     * {
     * "fanUserinfoId": 8622493,
     * "score": "i",
     * "isAttention": -1,
     * "isDisturb": 1,
     * "dealnum": 0,
     * "updateTime": 0
     * }
     * ]
     * }
     *
     */
    public function getFriendFanList(int $uid, string $score, int $limit, bool $assoc)
    {
        if ($uid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid必须大于0");
        }
        $params = [
            'uid' => $uid,
            'score' => $score,
            'limit' => $limit,
            'assoc' => $assoc,
        ];
        return $this->httpPost(Router::GET_FRIEND_FAN_LIST, $params);
    }

    /**
     * 设置粉丝消息没打扰
     * @param int $uid
     * @param int $fanUserinfoId
     * @param int $isDisturb
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1606392845,
     * "data": true
     * }
     *
     */
    public function settingIsDisturb(int $uid, int $fanUserinfoId, int $isDisturb)
    {
        if ($uid <= 0 || $fanUserinfoId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid、fanUserinfoId必须大于0");
        }
        $params = [
            'uid' => $uid,
            'fanUserinfoId' => $fanUserinfoId,
            'isDisturb' => $isDisturb,
        ];
        return $this->httpPost(Router::SETTING_ISDISTURB, $params);
    }

    /**
     * 获取激活粉丝列表
     * @param int $uid
     * @param int $lastActiveTime
     * @param int $limit
     * @param int $offset
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1606390997,
     * "data": [
     * {
     * "fanUserinfoId": 8622493
     * }
     * ]
     * }
     */
    public function getActiveFanList(int $uid, int $lastActiveTime, int $limit, int $offset)
    {
        if ($uid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid必须大于0");
        }
        $params = [
            'uid' => $uid,
            'lastActiveTime' => $lastActiveTime,
            'limit' => $limit,
            'offset' => $offset,
        ];
        return $this->httpPost(Router::GET_ACTIVE_FAN_LIST, $params);
    }

    /**
     * 异步更新粉丝活跃时间
     * @param int $uid
     * @param int $fanUserinfoId
     * @param int $activeTime
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1606391007,
     * "data": true
     * }
     */
    public function updateFanActiveTimeAsync(int $uid, int $fanUserinfoId, int $activeTime)
    {
        if ($uid <= 0 || $fanUserinfoId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid、fanUserinfoId必须大于0");
        }
        $params = [
            'uid' => $uid,
            'fanUserinfoId' => $fanUserinfoId,
            'activeTime' => $activeTime,
        ];
        return $this->httpPost(Router::UPDATE_FAN_ACTIVE_TIME_ASYNC, $params);
    }

    /**
     * 获取直播间粉丝列表
     * @param int $uid
     * @param string $score
     * @param int $limit
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1606391013,
     * "data": [
     * {
     * "fanUserinfoId": 614,
     * "score": "spEqlHL3F"
     * },
     * {
     * "fanUserinfoId": 613,
     * "score": "spEqgEmTF"
     * }
     * ]
     * }
     */
    public function getLiveFanList(int $uid, string $score, int $limit)
    {
        if ($uid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid必须大于0");
        }
        $params = [
            'uid' => $uid,
            'score' => $score,
            'limit' => $limit,
        ];
        return $this->httpPost(Router::GET_LIVE_FAN_LIST, $params);
    }

    /**
     * 直播间 关注/取消关注
     * @param int $uid
     * @param int $attentionUid
     * @param int $isAttention
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1606391025,
     * "data": true
     * }
     */
    public function updateLiveAttention(int $uid, int $attentionUid, int $isAttention)
    {
        if ($uid <= 0 || $attentionUid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid、attentionUid必须大于0");
        }
        $params = [
            'uid' => $uid,
            'attentionUserinfoId' => $attentionUid,
            'isAttention' => $isAttention,
        ];
        return $this->httpPost(Router::UPDATE_LIVE_ATTENTION, $params);
    }

    /**
     * 获取直播间关注状态信息
     * @param int $uid
     * @param int $attentionUid
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1606391031,
     * "data": {
     * "isAttention": 0,
     * "updateTime": 1606391025
     * }
     * }
     */
    public function getLiveAttentionInfo(int $uid, int $attentionUid)
    {
        if ($uid <= 0 || $attentionUid <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "uid、attentionUid必须大于0");
        }
        $params = [
            'uid' => $uid,
            'attentionUserinfoId' => $attentionUid,
        ];
        return $this->httpPost(Router::GET_LIVE_ATTENTION_INFO, $params);
    }

    /**
     * 批量获取粉丝数
     * @param array $shopIds
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1606391040,
     * "data": {
     * "1": 7,
     * "2": 356,
     * "3": 43,
     * "4": 38,
     * "5": 43,
     * "6": 2,
     * "7": 17,
     * "8": 2
     * }
     * }
     */
    public function getFansNumBatch(array $shopIds)
    {
        if (empty($shopIds)) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopIds必须有值");
        }
        $params = [
            'shopIds'=>$shopIds,
        ];
        return $this->httpPost(Router::GET_FANS_NUM_BATH, $params);
    }
}
