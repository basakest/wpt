<?php


namespace WptBus\Service\Order\Module;


use WptBus\Lib\Utils;
use WptBus\Service\BaseService;
use WptBus\Service\Order\Router;

class QuickDelivery extends BaseService
{
    /**
     * 根据用户获取发货日期
     * @param $userinfoId
     * @param $date
     * @return array|void
     */
    public function getQuickDeliveryList(int $userinfoId, array $date,int $limit, int $offset)
    {
        $params = ["userinfoId" => (int)$userinfoId, "date" => $date, "limit" => $limit, "offset" => $offset];
        if ($error = $this->validate($params, [
                'userinfoId' => 'required|integer|min:1',
                'date' => 'required'
            ]
        )) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_ORDER_QUICK_DELIVERY_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, 0);
        });
        return $ret;
    }

    /**
     * 根据用户生成导出发货数据
     * @param $userinfoId
     * @param $date
     * @return array|void
     */
    public function exportQuickDeliveryList(int $userinfoId, array $date, int $type)
    {
        $params = ["userinfoId" => (int)$userinfoId, "date" => $date, "type" => $type];
        if ($error = $this->validate($params, [
                'userinfoId' => 'required|integer|min:1',
                'date' => 'required'
            ]
        )) {
            return $error;
        }
        $ret = $this->httpPost(Router::EXPORT_ORDER_QUICK_DELIVERY_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;

        });
        return $ret;
    }

    /**
     * 根据用户统计首页日期
     * @param $userinfoId
     * @param $date
     * @return array|void
     */
    public function getQuickDeliveryTotalByDate(int $userinfoId, int $date)
    {
        $params = ["userinfoId" => (int)$userinfoId, "date" => $date];
        if ($error = $this->validate($params, [
                'userinfoId' => 'required|integer|min:1',
                'date' => 'required'
            ]
        )) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_ORDER_QUICK_DELIVERY_TOTAL_BY_DATE, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    /**
     * 根据用户统计首页日期
     * @param $userinfoId
     * @param $date
     * @return array|void
     */
    public function getQuickDeliveryCountByUser(int $userinfoId, array $date)
    {
        $params = ["userinfoId" => (int)$userinfoId, "date" => $date];
        if ($error = $this->validate($params, [
                'userinfoId' => 'required|integer|min:1',
                'date' => 'required'
            ]
        )) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_ORDER_QUICK_DELIVERY_COUNT_BY_USER, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }


}