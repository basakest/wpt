<?php


namespace WptBus\Service\User\Module;


use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class Address extends BaseService
{
    /**
     * 查询地址
     * @param int $uid 用户id
     * @param int $aid 地址id
     * @return array
     * {
     *  "code": 0,
     *  "msg": "",
     *  "nowTime": 1604582361,
     *  "data": {
     *      "addressCitySecondStageName": "石家庄市",
     *      "addressCountiesThirdStageName": "长安区",
     *      "addressDetailInfo": "老师开始面向开心",
     *      "nationalCode": "130102",
     *      "proviceFirstStageName": "河北省",
     *      "telNumber": "18106586291",
     *      "userName": "江俊",
     *      "addressId": 2870063
     *   }
     * }
     */
    public function getAddress(int $uid, int $aid)
    {
        return $this->httpPost(Router::GET_ADDRESS, [
            "uid" => $uid,
            "id" => $aid
        ]);
    }
    /**
     * 创建地址
     * @param int $uid 用户id
     * @param array $data 地址信息,类型应为 map[string]string类型（数组中的值类型也应为字符串）
     * {
     * "userName": "江俊2",
     * "proviceFirstStageName": "北京市1",
     * "addressCitySecondStageName": "北京市",
     * "addressCountiesThirdStageName": "北京市",
     * "addressDetailInfo": "aaaaa",
     * "telNumber": "15990058711"
     * }
     * @return array
     */
    public function createAddress(int $uid, array $data)
    {
        return $this->httpPost(Router::CREATE_ADDRESS, [
            "uid" => $uid,
            "data" => $data
        ]);
    }
    /**
     * 更新地址
     * @param int $uid 用户id
     * @param int $aid 地址id
     * @param array $data 地址信息,类型应为 map[string]string类型（数组中的值类型也应为字符串）
     * {
     * "userName": "江俊2",
     * "proviceFirstStageName": "北京市1",
     * "addressCitySecondStageName": "北京市",
     * "addressCountiesThirdStageName": "北京市",
     * "addressDetailInfo": "aaaaa",
     * "telNumber": "15990058711"
     * }
     * @return array
     */
    public function updateAddress(int $uid,int $aid, array $data)
    {
        return $this->httpPost(Router::UPDATE_ADDRESS, [
            "uid" => $uid,
            "id"  => $aid,
            "data" => $data
        ]);
    }

    /**
     * 删除地址
     * @param int $uid 用户id
     * @param int $aid 地址id
     * @return array
     * {"code":0,"msg":"","data":false,"nowTime":1604629226}
     */
    public function deleteAddress(int $uid, int $aid)
    {
        return $this->httpPost(Router::DELETE_ADDRESS, [
            "uid" => $uid,
            "id" => $aid
        ]);
    }

    /**
     * 基于uri更新或创建地址
     * @param int $uid 用户id
     * @param int $aid 地址id
     * @param array $data 地址信息,类型应为 map[string]string类型（数组中的值类型也应为字符串）
     * {
     * "userName": "江俊2",
     * "proviceFirstStageName": "北京市1",
     * "addressCitySecondStageName": "北京市",
     * "addressCountiesThirdStageName": "北京市",
     * "addressDetailInfo": "aaaaa",
     * "telNumber": "15990058711"
     * }
     * @return array
     */
    public function createOrUpdateByUri(int $uid,int $aid, array $data)
    {
        return $this->httpPost(Router::CRATE_OR_UPDATE_ADDRESS, [
            "uid" => $uid,
            "id"  => $aid,
            "data" => $data
        ]);
    }
    /**
     * @param int $uid 用户id
     * @param int $aid 地址id
     * @return array
     */
    public function SetDefaultShippingAddress(int $uid, int $aid)
    {
        $ret = $this->httpPost(Router::SET_DEFAULT_SHIPPING_ADDRESS, [
            "uid" => $uid,
            "aid" => $aid
        ]);
        return $ret;
    }

    /**
     * @param int $uid 用户id
     * @param int $aid 地址id
     * @return array
     */
    public function SetDefaultReturnAddress(int $uid, int $aid)
    {
        $ret = $this->httpPost(Router::SET_DEFAULT_RETURN_ADDRESS, [
            "uid" => $uid,
            "aid" => $aid
        ]);
        return $ret;
    }

    /**
     * @param int $uid 用户id
     * @return array
     *
    {
    "code": 0,
    "msg": "",
    "nowTime": 1598959469,
    "data": {
    "addressCitySecondStageName": "北京市",
    "addressCountiesThirdStageName": "东城区",
    "addressDetailInfo": "胡渣男",
    "addressPostalCode": "100010",
    "id": "10",
    "proviceFirstStageName": "北京市",
    "telNumber": "15853335699",
    "userName": "新发"
    }
    }
     */
    public function GetDefaultShippingAddress(int $uid)
    {
        $ret = $this->httpPost(Router::GET_DEFAULT_SHIPPING_ADDRESS, [
            "uid" => $uid
        ]);
        return $ret;
    }

    /**
     * @param int $uid 用户id
     * @return array
    {
    "code": 0,
    "msg": "",
    "nowTime": 1598959469,
    "data": {
    "addressCitySecondStageName": "北京市",
    "addressCountiesThirdStageName": "东城区",
    "addressDetailInfo": "胡渣男",
    "addressPostalCode": "100010",
    "id": "10",
    "proviceFirstStageName": "北京市",
    "telNumber": "15853335699",
    "userName": "新发"
    }
    }
     */
    public function GetDefaultReturnAddress(int $uid)
    {
        $ret = $this->httpPost(Router::GET_DEFAULT_RETURN_ADDRESS, [
            "uid" => $uid
        ]);
        return $ret;
    }

    /**
     * 获取地址列表
     * @param int $uid
     * @return array
     * {
    "code":0,
    "msg":"",
    "data":{
    "result":{
    "9":{
    "addressCitySecondStageName":"杭州市",
    "addressCountiesThirdStageName":"西湖区",
    "addressDetailInfo":"西湖区工专路景城花园",
    "addressPostalCode":"310013",
    "proviceFirstStageName":"浙江省",
    "telNumber":"1111111111",
    "userName":"zzzzz"
    },
    "1070001":{
    "addressCitySecondStageName":"杭州市",
    "addressCountiesThirdStageName":"西湖区",
    "addressDetailInfo":"文一路",
    "addressPostalCode":"310013",
    "proviceFirstStageName":"浙江省",
    "telNumber":"11111111",
    "userName":"aaa"
    }
    },
    "default":9, //默认收货地址id
    "return_default":9 // 默认退货地址id
    },
    "nowTime":1599623482
    }
     */
    public function GetList(int $uid)
    {
        $ret = $this->httpPost(Router::GET_ADDRESS_LIST, [
            "uid" => $uid
        ]);
        return $ret;
    }
}