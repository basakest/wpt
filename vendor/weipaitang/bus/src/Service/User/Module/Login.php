<?php

namespace WptBus\Service\User\Module;

use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

/**
 * 登录相关
 */
class Login extends BaseService
{
    /**
     * 登陆
     * @param int $platformId 平台类型Id
     * @param array $data 登陆数据
     * @return array
     */
    /*
    {
    "code": 0,
    "msg": "",
    "nowTime": 1587985323,
        "data": {
            "userinfoId": 0,            // 用户id
            "uri": "",                  // 用户uri
            "openid": "",               // 用户uri
            "isNew": false,             // 是否是新用户
            "isFirstRegister": false,   // 是否是第一次注册用户
            "platformInfo": {           // 对应平台账号信息
                "platformId": 0,        // 平台id
                "originalUid": 0,       // 平台对应uid
                "originalUri": "",      // 平台对应uri
                "originalOpenid": ""    // 平台对应openid
            },
            "token":"aaa.bbb.ccc",      // token
            "expireTime":0              // token 过期时间戳
        }
    }
     */
    public function login(int $platformId, array $data)
    {
        if (in_array($platformId, [32, 34])) {
            $httpConfig = ["readTimeout" => 3000];
            $this->setHttpConfig($httpConfig);
        }
        $ret = $this->httpPost(Router::AUTH_LOGIN, [
            "platformId" => $platformId,
            "data" => $data,
        ]);
        if ($ret["code"] == 202116) { // 10分钟内禁止验证 提示 验证码错误
            $ret["code"] = 202029;
            $ret["msg"] = "验证码错误";
        }
        return $ret;
    }

    /**
     * 根据token获取过去时间和所属用户基本信息
     * @param boolean $isNeedUserInfo 是否需要用户信息
     * @param array $credentials 是否需要用户信息
     * @return array
     */
    /*
     {
        "code":0,
        "msg":"",
        "nowTime":1599737960,
            "data":{
            "userinfoId":0,     // 用户唯一标识
            "uri":"",           // 用户对外唯一标识
            "openid":"",        // 用户 openid
            "nickname":"",      // 昵称
            "headimgurl":"",    // 头像
            "signature":"",     // 签名
            "sex":0,            // 性别 0未知,1男,2女
            "region":"",        // 区域
            "country":"",       // 国家
            "province":"",      // 省份
            "city":"",          // 城市
            "lang":"",          // 语言
            "expireTime":0      // token 过期时间
        }
    }
     */
    public function authenticate(bool $isNeedUserInfo, array $credentials)
    {
        $ret= $this->httpPost(Router::AUTH_AUTHENTICATE, [
            "isNeedUserInfo" => $isNeedUserInfo,
            "credentials" => $credentials,
        ]);

        $this->dealResultData($ret, function ($data) {
            return $data ? json_decode($data, true) : [];
        });
        return $ret;
    }

    /**
     * 刷新 token
     * @param string $token
     * @return array
     */
    /*
     {
        "code": 0,
        "msg": "",
        "nowTime": 1599737522,
        "data": {
            "token": "aaa.bbb.ccc", // 新token
            "expireTime": 0         // 新token过期时间戳
        }
    }
     */
    public function refreshToken(string $token)
    {
        return $this->httpPost(Router::AUTH_REFRESH_TOKEN, [
            "token" => $token,
        ]);
    }

    /**
     * 登录日志记录列表
     * @param $userinfoId
     * @param int $limit
     * @param int $offset
     * @param string $order
     * @return array
     * [{
     *    "id": 1, // 主键
     *    "userinfoId": 1,// 用户ID
     *    "platformId": 0,// 平台ID
     *    "deviceId": "", // 设备ID
     *    "deviceModel": "",// 设备类型
     *    "ip": "", // 登录ip地址
     *    "loginTime": 0,// 登录时间
     *    "loginPlace": "{\"isp\": \"电信\", \"city\": \"杭州\", \"country\": \"中国\", \"province\": \"浙江\"}", // 登录地址
     *    "createTime": 0, // 创建时间
     *    "updateTime": 0 // 修改时间
     *}]
     */
    public function getLoginLogList($userinfoId,$limit = 100,$offset = 0, $order = "")
    {
        $parame = [
            'userinfoId' => (int)$userinfoId,
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'order' => (string)$order,
        ];

        return $this->httpPost(Router::GET_LOGIN_LOG_LIST, $parame);
    }
    /**
     * 创建code
     * @return array
     *
     * {
     *  "code": 0,
     *  "msg": "",
     *  "nowTime": 1604646326,
     *  "data": {
     *      "code": "2f5178e951cf49928fab1bd5cfbc14e3",
     *      "expireTime": 1604646386
     *  }
     * }
     *
     */
    public function createCode()
    {
        $param = [];
        return $this->httpPost(Router::CREATE_CODE, $param);
    }

    /**
     * 获取code
     * @param $code
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604649850,
     * "data": {
     *      "code": "7072572db936463ca69f1312177a500f",
     *      "status": "Checked",
     *      "expireTime": 1604649907,
     *      "uid": 10,
     *      "extra": ""
     *    }
     * }
     *
     */
    public function getCode(string $code)
    {
        $param = [
            'code' => (string)$code,
        ];
        return $this->httpPost(Router::GET_CODE, $param);
    }

    /**
     * 校验code
     * @param $code
     * @param $uid
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604649850,
     * "data": ""
     *
     */
    public function checkCode(int $uid,string $code)
    {
        $param = [
            'uid' => (int)$uid,
            'code' => (string)$code,
        ];
        return $this->httpPost(Router::CHECK_CODE, $param);
    }

    /**
     * 确认code
     * @param $code
     * @param $uid
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604649850,
     * "data": ""
     *
     */
    public function confirmCode(int $uid,string $code)
    {
        $param = [
            'uid' => (int)$uid,
            'code' => (string)$code,
        ];
        return $this->httpPost(Router::CONFIRM_CODE, $param);
    }

    /**
     * 取消code
     * @param $code
     * @param $uid
     * @return array
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1604649850,
     * "data": ""
     *
     */
    public function cancelCode(int $uid,string $code)
    {
        $param = [
            'uid' => (int)$uid,
            'code' => (string)$code,
        ];
        return $this->httpPost(Router::CANCEL_CODE, $param);
    }
}
