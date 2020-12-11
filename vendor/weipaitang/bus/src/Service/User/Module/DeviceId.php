<?php


namespace WptBus\Service\User\Module;


use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class DeviceId extends BaseService
{
    /**
     * 基于用户ID查询设备
     * @param $uid
     * @return array
     * {
     *   "code": 0,
     *   "msg": "",
     *   "nowTime": 1603934764,
     *   "data": {
     *      "devices": [
     *          {
     *              "deviceId": "00129cceafcda405"
     *          }
     *       ]
     *    }
     * }
     */
    public function getDeviceByUid(int $uid)
    {
        $param = [
            'uid' => (int)$uid,
        ];
        return $this->httpPost(Router::GET_DEVICE_BY_UID, $param);
    }

    /**
     * 基于设备ID查询用户ID
     * @param $deviceId
     * @return array
     * {
     *   "code": 0,
     *   "msg": "",
     *   "nowTime": 1603934764,
     *   "data": {
     *      "uids": [
     *          75767426
     *       ]
     *    }
     * }
     */
    public function getUidByDeviceId(string $deviceId)
    {
        $param = [
            'deviceId' => (string)$deviceId,
        ];
        return $this->httpPost(Router::GET_UID_BY_DEVICE_ID, $param);
    }

    /**
     * 是否是绑定过的设备
     * @param $deviceId
     * @param $uid
     * @return array
     * {
     *   "code": 0,
     *   "msg": "",
     *   "nowTime": 1603934764,
     *   "data": {
     *      "isBound": true
     *    }
     * }
     */
    public function isBoundDevice(string $deviceId,int $uid)
    {
        $param = [
            'uid' => (int)$uid,
            'deviceId' => (string)$deviceId,
        ];
        return $this->httpPost(Router::IS_BOUND_DEVICE, $param);
    }

    /**
     * 是否是新设备
     * @param $deviceId
     * @return array
     * {
     *   "code": 0,
     *   "msg": "",
     *   "nowTime": 1603934764,
     *   "data": {
     *      "isNew": true
     *    }
     * }
     */
    public function isNewDevice(string $deviceId)
    {
        $param = [
            'deviceId' => (string)$deviceId,
        ];
        return $this->httpPost(Router::IS_NEW_DEVICE, $param);
    }
}