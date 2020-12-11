<?php


namespace WptBus\Service\Shop\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Shop\Router;

class ShopReport extends BaseService
{
    /**
     * 查询店铺报表基础属性信息
     * @param $userinfoId
     * @param array $properties
     * @return array
     */
    public function inquire($userinfoId, array $properties)
    {
        $params = ["uid" => intval($userinfoId), "properties" => $properties];

        return $this->httpPost(Router::GET_SHOP_REPORT, $params);
    }

    /**
     * 店铺今日的资金报表（成拍、已付款、已退款、已收款、未发货、待确人、退款中、退货中的钱款和交易数）
     * 老接口3s超时
     * @param int $userinfoId
     * @return array
     */
    public function shopTodayCapitalReport(int $userinfoId)
    {
        $params = ['uid' => $userinfoId];
        $this->setTimeout(3000);
        return $this->httpPost(Router::GET_TODAY_CAPITAL, $params);
    }

    /**
     * 店铺的pv和uv
     * @param string $userinfoUri
     * @return array
     */
    public function shopTodayPUVReport(string $userinfoUri)
    {
        $params = ['uri' => $userinfoUri];

        return $this->httpPost(Router::GET_TODAY_PUV, $params);
    }

    /**
     * 店铺今日上拍数
     * @param int $userinfoId
     * @return array
     */
    public function shopTodaySaleNumReport(int $userinfoId)
    {
        $params = ['uid' => $userinfoId];

        return $this->httpPost(Router::GET_TODAY_PUBLISH, $params);
    }

    /**
     * 店铺的今日付款人数和新客占比
     * @param int $userinfoId
     * @return array
     */
    public function shopTodayPaidAndNewCustomer(int $userinfoId)
    {
        $params = ['uid' => $userinfoId];

        return $this->httpPost(Router::GET_TODAY_BUYER_AND_NEW_CUSTOMER, $params);
    }

}