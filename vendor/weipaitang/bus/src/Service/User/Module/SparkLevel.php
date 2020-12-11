<?php


namespace WptBus\Service\User\Module;

use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class SparkLevel extends BaseService
{
    // 设置商家星火等级接口
    public function setUserinfoSparkLevel(int $userinfoId, int $sparkLevel, string $theDate)
    {
        $params = ["userinfoId" => $userinfoId,"sparkLevel" => $sparkLevel, "theDate" => $theDate];
        return $this->httpPost(Router::SET_USERINFO_SPARK_LEVEL, $params);
    }

    // 批量查询商家星火等级接口,数组中类型应为int
    public function getUserinfoSparkLevelList(array $userinfoIds)
    {
        $params = ["uids" => $userinfoIds];
        return $this->httpPost(Router::GET_USERINFO_SPARK_LEVEL_LIST, $params);
    }

    // 根据星火等级获取商家id接口
    public function getUidsBySparkLevel(int $sparkLevel, string $orderBy, int $pageNum, int $pageSize)
    {
        $params = ["sparkLevel" => $sparkLevel, "orderBy" => $orderBy, "pageNum" => $pageNum, "pageSize" => $pageSize];
        return $this->httpPost(Router::GET_UIDS_BY_SPARK_LEVEL, $params);
    }

    // 批量获取店铺星火等级信息
    public function getShopSparkLevelInfo(array $shopIds)
    {
        if (count($shopIds) < 0 || count($shopIds) > 200) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopIds 长度1～200");
        }
        $params = ["shopIds" => array_values($shopIds)];
        return $this->httpPost(Router::GET_SHOP_SPARK_LEVEL_INFO, $params);
    }
}