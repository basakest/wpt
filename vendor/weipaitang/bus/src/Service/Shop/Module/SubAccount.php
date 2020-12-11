<?php

namespace WptBus\Service\Shop\Module;

use WptBus\Lib\Utils;
use WptBus\Service\BaseService;
use WptBus\Service\Shop\Router;

class SubAccount extends BaseService
{
    /**
     * 获取子账号 （迁移user-service sdk）
     * @param $userId
     * @param $masterId
     * @return mixed|null
     */
    public function getSubAccount($userId, $masterId)
    {
        $params = ["uid" => intval($userId), "masterUid" => intval($masterId)];
        $res = $this->httpPost(Router::GET_SUB_ACCOUNT, $params);
        return Utils::property($res, "data", []);
    }

    /**
     * 获取子账号列表（有过期时间的那种）根据userID （迁移user-service sdk）
     *    var where = map[string]interface{}{
     * "userinfoId":    uid,
     * "status":        1,
     * "expiredTime >": time.Now().Unix(),
     * }
     * @param $userId
     * @return mixed|null
     */
    public function getSubAccountListWithUserId($userId)
    {
        $params = ["uid" => intval($userId)];
        $res = $this->httpPost(Router::GET_SUB_ACCOUNT_LIST_WITH_UID, $params);
        return Utils::property($res, "data", []);
    }


    /**
     * 获取子账号列表，根据master userID （迁移user-service sdk）
     * @param $masterId
     * @return mixed|null
     */
    public function getSubAccountListWithMasterUserId($masterId)
    {
        $params = ["uid" => intval($masterId)];
        $res = $this->httpPost(Router::GET_SUB_ACCOUNT_LIST_WITH_MASTER_UID, $params);
        return Utils::property($res, "data", []);
    }

    /**
     * 获取子账号数量，根据userID （迁移user-service sdk）
     * @param $userId
     * @return mixed|null
     */
    public function countSubAccountWithUserId($userId)
    {
        $params = ["uid" => intval($userId)];
        $res = $this->httpPost(Router::GET_COUNT_SUB_ACCOUNT_WITH_UID, $params);
        return Utils::property($res, "data", 0);
    }

    /**
     * 获取子账号数量，根据master userID （迁移user-service sdk）
     * @param $masterId
     * @return mixed|null
     */
    public function countSubAccountWithMasterUserId($masterId)
    {
        $params = ["uid" => intval($masterId)];
        $res = $this->httpPost(Router::GET_COUNT_SUB_ACCOUNT_WITH_MASTER_UID, $params);
        return Utils::property($res, "data", 0);
    }

    /**
     * 创建子账号 （迁移user-service sdk）
     * @param $userId
     * @param $masterId
     * @param $data
     * @return bool
     */
    public function createSubAccount($userId, $masterId, $data)
    {
        $params = ["uid" => intval($userId), "masterUid" => intval($masterId), "data" => json_encode($data)];
        $res = $this->httpPost(Router::CREATE_SUB_ACCOUNT, $params);
        $code = Utils::property($res, "code", -1);
        return $code == 0;
    }

    /**
     * 更新子账号 （迁移user-service sdk）
     * @param $userId
     * @param $masterId
     * @param $data
     * @return bool
     */
    public function updateSubAccount($userId, $masterId, $data)
    {
        $params = ["uid" => intval($userId), "masterUid" => intval($masterId), "data" => json_encode($data)];
        $res = $this->httpPost(Router::UPDATE_SUB_ACCOUNT, $params);
        $code = Utils::property($res, "code", -1);
        return $code == 0;
    }

    /**
     * 获取是永久的子账号 （迁移user-service sdk）
     * 实现的条件：
     * "userinfoId":  uid,
     * "status":      1,
     * "expiredTime": 0,
     * @param $userId
     * @return mixed|null
     */
    public function getSubAccountListWithUserIdAll($userId)
    {
        $params = ["uid" => intval($userId)];
        $res = $this->httpPost(Router::GET_SUB_ACCOUNT_LIST_WITH_UID_ALL, $params);
        return Utils::property($res, "data", []);
    }
}