<?php


namespace WptBus\Service\Shop\Module;

use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Service\BaseService;
use WptBus\Service\Shop\Router;

class ShopSetting extends BaseService
{
    /**
     * 设置店铺联系人信息
     * @param int $shopId
     * @param array $fields
     * @return array
     *
     * 异常情况示例：
     * {
     * "code": 202029,
     * "msg": "请输入正确的紧急联系号码",
     * "nowTime": 1603961830,
     * "data": null
     * }
     *
     * 设置成功示例：
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1603961674,
     * "data": {
     * "isOk": true
     * }
     * }
     *
     */
    public function setShopContact(int $shopId, array $fields) {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 应大于0");
        }
        if (count($fields) == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "fields 必须");
        }

        $parame = [
            'shopId' => $shopId,
            'fields' => $fields,
        ];

        return $this->httpPost(Router::SET_SHOP_CONTACT_INFO, $parame);
    }


    /**
     * 获取店铺联系人信息
     * @param int $shopId
     * @param array $fields
     * @return array
     *
     * 有数据示例：
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1603961767,
     * "data": {
     * "id": 0,
     * "shopId": 0,
     * "contacts": "xiaoming",
     * "emergencyTel": "16605818888",
     * "createTime": 0,
     * "updateTime": 0,
     * "modifyTime": ""
     * }
     * }
     *
     * 无数据示例：
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1603961736,
     * "data": null
     * }
     */
    public function getShopContact(int $shopId, array $fields) {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 应大于0");
        }
        if (count($fields) == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "fields 必须");
        }

        $parame = [
            'shopId' => $shopId,
            'fields' => $fields,
        ];
        return $this->httpPost(Router::GET_SHOP_CONTACT_INFO, $parame);
    }

    /**
     * 获取常用快递
     * @param int $shopId
     * @return array
     *
     * 有数据示例：
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604631439,
     * "data": [
     *      "huitongkuaidi"
     *  ]
     * }
     *
     * 无数据示例：
     * {"code":0,"msg":"","data":[""],"nowTime":1604633038}
     */
    public function getDeliveryCom(int $shopId) {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 应大于0");
        }

        $param = [
            'uid' => $shopId,
        ];
        return $this->httpPost(Router::GET_DELIVERY_COM, $param);
    }

    /**
     * 获取常用快递
     * @param int $shopId
     * @param array $deliveryCom
     * @return array
     */
    public function setDeliveryCom(int $shopId,array $deliveryCom) {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 应大于0");
        }
        $data = json_encode(["deliveryComJson" => $deliveryCom]);
        $param = [
            'uid' => $shopId,
            'data' => $data
        ];
        return $this->httpPost(Router::SET_DELIVERY_COM, $param);
    }
}