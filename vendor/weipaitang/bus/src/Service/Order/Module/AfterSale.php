<?php


namespace WptBus\Service\Order\Module;


use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Service\BaseService;
use WptBus\Service\Order\Router;

class AfterSale extends BaseService
{

    /**
     * 获取售后的未完成的退货单
     * @param $orderId
     * @return array
     */
    public function getAfterSaleActiveInfo($orderId)
    {
        return $this->getAfterSaleInfo($orderId, 1);
    }

    /**
     * 获取售后的已经完成的退货单
     * @param $orderId
     * @return array
     */
    public function getAfterSaleCloseInfo($orderId)
    {
        return $this->getAfterSaleInfo($orderId, 0);
    }

    /**
     * 获取售后的未完和已经完成的退货单
     * @param $orderId
     * @return array
     */

    public function getAfterSaleAllInfo($orderId)
    {
        return $this->getAfterSaleInfo($orderId, -1);
    }

    /**
     * 获取售后的退货单
     * @param $saleId
     * @param int $isActive
     * @return array
     */
    private function getAfterSaleInfo($saleId, $isActive = 1)
    {
        if ($saleId == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT);
        }

        $where = ['orderId' => $saleId, 'result' => 'agreeReturn', 'isMoneyReturn' => 1];
        if ($isActive != -1) {
            $where['isActive'] = $isActive;
        }
        $params['where'] = json_encode($where, JSON_UNESCAPED_UNICODE);
        $params['limit'] = 1;
        $params['offset'] = 0;
        $params['orderBy'] = "createTime desc";
        $params['fields'] = [
            'id',
            'orderId',
            'userinfoId',
            'returnToUserId',
            'returnToAddress',
            'returnToDelivery',
            'returnDeliveryTime',
            'createTime',
            'reasonId',
            'reason',
            'returnStatus',
            'expectRefundFee'
        ];
        $ret = $this->httpPost(Router::GET_ORDER_RETURN_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            if (count($data) == 0) {
                return [];
            }
            return $data[0];
        });
        return $ret;
    }

    /**
     * 获取售后的退货单列表
     * @param $where
     * @param $fields
     * @param string $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAfterSaleList($where, $fields = [], $limit = 200, $offset = 0, $orderBy = "createTime desc")
    {
        $params['where'] = json_encode($where, JSON_UNESCAPED_UNICODE);
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        $params['orderBy'] = $orderBy;
        $params['fields'] = $fields;
        if ($error = $this->validate($params, ['where' => 'required', 'fields' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_ORDER_RETURN_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            if (count($data) == 0) {
                return [];
            }
            return $data;
        });
        return $ret;
    }


}