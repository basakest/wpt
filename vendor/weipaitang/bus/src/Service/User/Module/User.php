<?php


namespace WptBus\Service\User\Module;


use stdClass;
use WptBus\Lib\Utils;
use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class User extends BaseService
{
    /**
     * 基础信息
     * @param string $identity
     * @param array $fields
     * [
     * "userinfoId",
     * "uri",
     * "nickname",
     * "headimgurl",
     * "sex",
     * "signature",
     * "country",
     * "lang",
     * "province",
     * "region",
     * "city",
     * "memberTime",
     * "memberLevel",
     * "bigCustomLevel",
     * "riskLevel",
     * "systemBzjLevel",
     * "isForbidden",
     * "isBindTelephone"
     * "loginInfo",
     * "userType"
     * ]
     *
     * @return array|void
     *
     * {
     * "userinfoId":8611679,
     * "uri":"1804201910x7D5gd",
     * "nickname":"啊啊啊",
     * "headimgurl":"https://cdn01t.weipaitang.com/mmopen/20200316d8tpuhan-n9zo-qjul-tddc-ozckzvzbsryw/w/0",
     * "signature":"222",
     * "sex":"1",
     * "country":"CN",
     * "lang":"ZH_CN",
     * "city":"株洲市",
     * "province":"湖南省",
     * "region":"详细地区",
     * "memberLevel":6,
     * "memberTime":1524222657,
     * "bigCustomLevel":1, // 大客户等级
     * "riskLevel":1,
     * "systemBzjLevel":2 // 系统保证金等级
     * "isBindTelephone":true, // 是否绑定手机号
     * "isForbidden":false, // 全站拉黑
     * "loginInfo":{
     * "lastLoginTime":1557740364, // 最后一次登陆时间
     * "firstLoginTime":1523436598 // 第一次注册时间
     * },
     * "userType":5
     * }
     */
    public function getBaseInfo(string $identity, array $fields)
    {
        $data = ["identity" => $identity, "field" => $fields];
        if ($error = $this->validate($data, ["identity" => "required|string", "field" => "required|array"])) {
            return $error;
        }
        $this->setTimeout(1500)->setRetryTimes(1);
        $ret = $this->httpPost(Router::INFO_GET_BASE_INFO, $data);
        $this->dealResultData($ret, function ($data) {
            return $data ? json_decode($data, true) : [];
        });
        return $ret;
    }

    /**
     * 批量获取用户信息，请求和返回同上，一次最多20个
     * @param array $identityList
     * @param array $fields
     * @return array|void
     */
    public function getBaseInfoBatch(array $identityList, array $fields)
    {
        foreach ($identityList as &$item) {
            $item = (string)$item;
        }
        $data = ["identityList" => $identityList, "field" => $fields];
        if ($error = $this->validate($data, ["identityList" => "required|array", "field" => "required|array"])) {
            return $error;
        }
        $this->setTimeout(1500)->setRetryTimes(1);
        $ret = $this->httpPost(Router::INFO_GET_BASE_INFO_BATCH, $data);
        $this->dealResultData($ret, function ($data) {
            return $data ? json_decode($data, true) : [];
        });
        return $ret;
    }

    /**
     * 隐私信息
     * @param int $uid
     * @param array $fields
     * [
     * "name",
     * "idCode",
     * "telephone"
     * ]
     * @return array|void
     *
     * {
     * "idCode":"430281141111",
     * "name":"涛",
     * "telephone":"18768495141"
     * }
     */
    public function getPrivacyInfo(int $uid, array $fields)
    {
        $data = ["uid" => $uid, "field" => $fields];
        if ($error = $this->validate($data, ["uid" => "required|int", "field" => "required|array"])) {
            return $error;
        }
        $this->setTimeout(1500)->setRetryTimes(1);
        $ret = $this->httpPost(Router::INFO_GET_PRIVACY_INFO, $data);
        $this->dealResultData($ret, function ($data) {
            return $data ? json_decode($data, true) : [];
        });
        return $ret;
    }

    /**
     * 批量获取隐私信息
     * @param array $uids
     * @param array $fields
     * ]
     */
    public function getPrivacyInfoBatch(array $uids, array $fields)
    {
        $data = ["uids" => $uids, "fields" => $fields];
        if ($error = $this->validate($data, ["uids" => "required|array", "fields" => "required|array"])) {
            return $error;
        }
        $this->setTimeout(1500)->setRetryTimes(1);
        $ret = $this->httpPost(Router::INFO_GET_PRIVACY_INFO_BATCH, $data);
        $this->dealResultData($ret, function ($data) {
            return $data ? json_decode($data, true) : [];
        });
        return $ret;
    }

    /**
     * 获取微信，抖音，百度等第三方信息
     * @param int $uid
     * @param int $platformId
     * @return array|void
     * {
     * "openId": "o3pxd1nNUY417LRZhNJ5sQEK-b3k",
     * "isSub": 1
     * }
     */
    public function getThirdInfo(int $uid, int $platformId)
    {
        $data = ["uid" => $uid, "userType" => $platformId];
        if ($error = $this->validate($data, ["uid" => "required|int", "userType" => "required|int"])) {
            return $error;
        }
        $this->setTimeout(1500)->setRetryTimes(1);
        return $this->httpPost(Router::INFO_GET_THIRD_INFO, $data);
    }


    /**
     * 获取微信，抖音，百度等第三方信息，根据platformId批量
     * @param int $uid
     * @param array $platformIdList
     * @return array|void
     * 同上批量
     */
    public function getThirdInfoTypeBatch(int $uid, array $platformIdList)
    {
        $platformIdList = array_map('intval', $platformIdList);
        $data = ["uid" => $uid, "userType" => $platformIdList];
        if ($error = $this->validate($data, ["uid" => "required|int", "userType" => "required|array"])) {
            return $error;
        }
        $this->setTimeout(1500)->setRetryTimes(1);
        return $this->httpPost(Router::INFO_GET_THIRD_INFO_TYPE_BATCH, $data);
    }
    /**
     * 查询偏好设置
     * @param int $uid
     * @param array $fields
     * [
     * "autoAttention"
     * ]
     * @return array|void
     *
     * {
     * "autoAttention":0
     * }
     */
    public function getPreference(int $uid, array $fields)
    {
        $param = ["uid" => $uid, "field" => $fields];
        $ret = $this->httpPost(Router::GET_PREFERENCE, $param);
        $this->dealResultData($ret, function ($data) {
            return $data ? json_decode($data, true) : [];
        });
        return $ret;
    }
    /**
     * 修改偏好设置
     * @param int $uid
     * @param string $field autoAttention
     * @param string $value
     * @return array
     */
    public function setPreference(int $uid, string $field, $value)
    {
        $data = json_encode(["field" => $field, "value" => $value]);
        return $this->httpPost(Router::SET_PREFERENCE, [
            "uid" => $uid,
            "data" => $data,
        ]);
    }
    /**
     * 查询最后支付方式
     * @param int $uid
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604468220,
     * "data": {"lastPayMethod":"wechat","tradePassword":"xxx"},
     * }
     */
    public function getLastPayMethod(int $uid)
    {
        return $this->httpPost(Router::GET_LAST_PAY_METHOD, [
            "uid" => $uid,
        ]);
    }
    /**
     * 更新最后支付方式
     * @param int $uid
     * @param string $method
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604468220,
     * "data": "",
     * }
     */
    public function updateLastPayMethod(int $uid,string $method)
    {
        return $this->httpPost(Router::UPDATE_LAST_PAY_METHOD, [
            "uid" => $uid,
            "method" => $method,
        ]);
    }
    /**
     * 更新用户余额
     * @param int $uid
     * @param int $money
     * @param int $frozen
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604468220,
     * "data": "",
     * }
     */
    public function updateBalance(int $uid,int $money,int $frozen)
    {
        return $this->httpPost(Router::UPDATE_BALANCE, [
            "uid" => $uid,
            "money" => $money,
            "frozen" => $frozen,
        ]);
    }
    /**
     * 更新用户消宝金
     * @param int $uid
     * @param int $money
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604468220,
     * "data": "",
     * }
     */
    public function updateBail(int $uid,int $money)
    {
        return $this->httpPost(Router::UPDATE_BAIL, [
            "uid" => $uid,
            "money" => $money,
        ]);
    }
    /**
     * 获取用户余额免密信息
     * @param int $uid
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604468220,
     * "data": [],
     * }
     */
    public function getBnpJson(int $uid)
    {
        return $this->httpPost(Router::GET_BNP_JSON, [
            "uid" => $uid,
        ]);
    }
    /**
     * 更新用户余额免密信息
     * @param int $uid
     * @param string $bnp
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604468220,
     * "data": [],
     * }
     */
    public function updateBnpJson(int $uid,string $bnp)
    {
        return $this->httpPost(Router::UPDATE_BNP_JSON, [
            "uid" => $uid,
            "bnpJson" => $bnp,
        ]);
    }
    /**
     * 更新扫码推广渠道
     * @param int $uid
     * @param string $scene
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604468220,
     * "data": [],
     * }
     */
    public function updateScene(int $uid,string $scene)
    {
        return $this->httpPost(Router::UPDATE_SCENE, [
            "uid" => $uid,
            "scene" => $scene,
        ]);
    }

    /**
     * 更新是否机器人
     * @param int $uid
     * @param int $platformId
     * @return array
     */
    public function updateIsRobot(int $uid, int $platformId)
    {
        return $this->httpPost(Router::UPDATE_IS_ROBOT, [
            "uid" => $uid,
            "platformId" => $platformId,
        ]);
    }

    /**
     * 查询用户风险信息
     * @param int $uid
     * @param int $type 类型 1:buyer 2:shop
     * @return array
     * {
     *   "code": 0,
     *   "msg": "",
     *   "nowTime": 1605085447,
     *   "data": {
     *     "userinfoId": 8610313,
     *     "status": 1,
     *     "endTime": 1581330266,
     *     "reason": "成交不卖"
     *  }
     * }
     * 没有查询到
     * {
     *  "code": 0,
     *  "msg": "",
     *  "nowTime": 1605088708,
     *  "data": {
     *    "userinfoId": 0,
     *    "status": 0,
     *    "endTime": 0,
     *    "reason": ""
     *   }
     * }
     */
    public function getRiskList(int $uid,int $type)
    {
        return $this->httpPost(Router::RISK_LIST_GET, [
            "uid" => $uid,
            "type" => $type,
        ]);
    }

    /**
     * 查询指定类型风险用户IDs
     * @param int type
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604468220,
     * "data": [],
     * }
     */
    public function getRiskListIds(int $type)
    {
        $ret = $this->httpPost(Router::RISK_LIST_GET_ID_LIST, [
            "type" => $type,
        ]);
        if (empty($ret["data"])) {
            $ret["data"] = new stdClass();
        }
        return $ret;
    }

    /**
     * 通过身份证号码获取用户数据条数
     * @param string $idCode
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1605258567,
     * "data": 9
     * }
     */
    public function getCountByIdCode(string $idCode)
    {
        return $this->httpPost(Router::GET_COUNT_BY_ID_CODE, [
            "idCode" => $idCode,
        ]);
    }
}