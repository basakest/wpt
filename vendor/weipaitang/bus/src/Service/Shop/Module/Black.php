<?php


namespace WptBus\Service\Shop\Module;

use WptBus\Lib\Utils;
use WptBus\Service\BaseService;
use WptBus\Service\Shop\Router;

class Black extends BaseService
{
    /**
     * 拉黑列表（迁移user-service sdk）
     * @param int $userId
     * @return array
     */
    public function getBlackList(int $userId)
    {
        $params = ["uid" => $userId];
        $res = $this->httpPost(Router::GET_BLACK_LIST, $params);

        if (isset($res["code"]) && $res["code"] == 0) {
            return Utils::property($res, "data", []);
        }
        return [];
    }

    /**
     * 拉黑数（迁移user-service sdk）
     * @param int $userId
     * @return int
     */
    public function getBlackNums(int $userId)
    {
        $params = ["uid" => $userId];
        $res = $this->httpPost(Router::GET_BLACK_NUMS, $params);
        if (isset($res["code"]) && $res["code"] == 0) {
            return Utils::property($res, "data", 0);
        }
        return 0;
    }

    /**
     * 被拉黑列表（迁移user-service sdk）
     * @param int $userId
     * @return array
     */
    public function getBeBlackList(int $userId)
    {
        $params = ["uid" => $userId];
        $res = $this->httpPost(Router::GET_BE_BLACK_LIST, $params);
        if (isset($res["code"]) && $res["code"] == 0) {
            return Utils::property($res, "data", []);
        }
        return [];
    }

    /**
     * 拉黑/取消拉黑（迁移user-service sdk）
     * @param int $userId
     * @param int $toUserId
     * @param bool $isBlack
     * @param int $exp
     * @return bool
     */
    public function updateBlack(int $userId, int $toUserId, bool $isBlack, int $exp = 0)
    {
        $params = ["uid" => $userId, "toUid" => $toUserId, "isBlack" => $isBlack, "exp" => $exp];
        $res = $this->httpPost(Router::UPDATE_BLACK, $params);
        if (!isset($res["code"])) {
            return false;
        }
        if (Utils::property($res, "code", 0) == 202067) {
            return false;
        }
        return true;
    }


    /**
     * 一口价黑名单（迁移user-service sdk）
     * @param string $userinfoId
     * @return bool
     */
    public function getStandardGoodsPublishBlacklist(string $userinfoId)
    {
        if ($userinfoId == '') {
            return false;
        }
        $params = [
            'userinfoId' => intval($userinfoId),
        ];

        $res = $this->httpPost(Router::GET_STANDARD_GOODS_BLACK, $params);

        if (!isset($res['code'])) {
            return false;
        }
        if ($res['code'] >= 200000) {
            return false;
        }
        $result = $res['data']['result'] ?? 0;
        if ($result > 0) {
            return true;
        }
        return false;
    }


}