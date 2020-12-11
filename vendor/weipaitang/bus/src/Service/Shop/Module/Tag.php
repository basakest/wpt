<?php


namespace WptBus\Service\Shop\Module;

use WptBus\Lib\Utils;
use WptBus\Service\BaseService;
use WptBus\Service\Shop\Router;

class Tag extends BaseService
{
    /**
     * 获取店铺标签（迁移user-service sdk）
     * @param $userId
     * @param $tagId
     * @return array
     */
    public function getShopTag($userId, $tagId)
    {
        $params = ["uid" => intval($userId), "tagId" => intval($tagId)];

        $res = $this->httpPost(Router::GET_SHOP_TAG, $params);
        $data = Utils::property($res, "data", []);
        $result = [];
        foreach ($data as $key => $value) {
            if ($key == "dataJson") {
                $result["data"] = json_decode($value, true);
                continue;
            }
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * 获取店铺标签列表（迁移user-service sdk）
     * @param $userId
     * @return array
     */
    public function getShopTagList($userId)
    {
        $params = ["uid" => intval($userId)];
        $res = $this->httpPost(Router::GET_SHOP_TAG_LIST, $params);
        $list = Utils::property($res, "data", []);
        $result = [];
        foreach ($list as $key => $item) {
            foreach ($item as $k => $v) {
                if ($k == "dataJson") {
                    $item["data"] = json_decode($v, true);
                    unset($item["dataJson"]);
                    break;
                }
            }
            $result[] = $item;
        }
        return $result;
    }

    /**
     * 更新店铺标签（迁移user-service sdk）
     * @param $userId
     * @param $tagId
     * @param $data
     * @return bool
     */
    public function updateShopTag($userId, $tagId, $data)
    {
        $params = ["uid" => intval($userId), "tagId" => intval($tagId), "data" => json_encode($data)];
        $res = $this->httpPost(Router::UPDATE_SHOP_TAG, $params);
        $code = Utils::property($res, "code", -1);
        return $code == 0 ? true : false;
    }

    /**
     * 评价标签列表
     * @param $uid
     * @return mixed
     */
    public function getUserRateTagList($uid)
    {
        $params = ["uid" => intval($uid)];
        $res = $this->httpPost(Router::GET_RATE_TAG_LIST, $params);
        return Utils::property($res, "data", []);
    }


}