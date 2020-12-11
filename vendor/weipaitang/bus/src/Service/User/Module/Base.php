<?php


namespace WptBus\Service\User\Module;


use WptBus\Lib\Utils;
use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class Base extends BaseService
{
    const MOBILE_UCENTER_VERIFY_TYPE_CODE = "code";
    const MOBILE_UCENTER_VERIFY_TYPE_ONE_CLICK_ALI = "oneClickAli";
    const MOBILE_UCENTER_VERIFY_TYPE_ONE_CLICK_ALI_VERIFY= "oneClickAliVerify";

    /**
     * 登陆
     * @param int $platformId 平台类型Id
     * @param array $data 登陆数据
     * @param string $scope 授权 system：系统授权
     * @return array
     */
    /*
    {
        "code": 0,
        "msg": "",
        "data": {
            "userinfoId": 3620252, // 用户id
            "uri": "1601281431EK9AwW", // 用户uri
            "openid": "oGddcsz37uTUrBoaYRSRDI4zHzDo", // 用户uri
            "isNew": false, // 是否是新用户
            "isFirstRegister": false, // 是否是第一次注册用户
            "platformInfo": { // 对应平台账号信息
                "platformId": 5, // 平台id
                "originalUid": 50356380, // 平台对应uid
                "originalUri": "2001151959ohWaSz", // 平台对应uri
                "originalOpenid": "tel_app_86_17620184931" // 平台对应openid
            }
        },
        "nowTime": 1587985323
    }
     */
    public function login(int $platformId, array $data, string $scope = "")
    {
        if (in_array($platformId, [32, 34])) {
            $httpConfig = ["readTimeout" => 3000];
            $this->setHttpConfig($httpConfig);
        }
        if ($scope) {
            $this->getSign($this->config['http'], $data);
        }
        $ret = $this->httpPost(Router::AUTH_LOGIN, [
            "platformId" => $platformId,
            "data" => $data,
            "scope" => $scope
        ]);
        if ($ret["code"] == 202116) { // 10分钟内禁止验证 提示 验证码错误
            $ret["code"] = 202029;
            $ret["msg"] = "验证码错误";
        }
        return $ret;
    }


    /**
     * 修改用户基本信息
     * @param int $uid
     * @param string $field name/signature/weixin/nickname/headimgurl/sex
     * @param string $data
     * @param bool $checkPhone 是否检查存在手机号码
     * @param array $extra 额外的信息，修改nickname需要原用户的uri和nickname
     * @return array
     */
    public function updateBaseInfoSingle(int $uid, string $field, string $data, bool $checkPhone = false, array $extra = [])
    {
        if ($error = $this->validate(["field" => $field], ['field' => 'required|in:name,signature,weixin,nickname,headimgurl,sex'])) {
            return $error;
        }
        if (empty($extra)) {
            $extra = ["uri" => "", "nickname" => ""];
        }
        $data = json_encode(["field" => $field, "data" => $data]);
        if ($error = $this->validate(["data" => $data], ['data' => 'required|string'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::INFO_UPDATE_BASE_INFO_BY_SINGLE, [
            "uid" => $uid,
            "data" => $data,
            "checkPhone" => $checkPhone,
            'extra' => $extra
        ]);
        return $ret;
    }

    /**
     * 修改地址信息
     * @param int $uid
     * @param array $data [region,province,city,postcode]
     * @return array
     */
    public function updateBaseInfoAddress(int $uid, array $data)
    {
        $ret = $this->httpPost(Router::INFO_UPDATE_BASE_INFO_BY_ADDRESS, [
            "uid" => $uid,
            "address" => [
                "region" => $data["region"] ?? "",
                "province" => $data["province"] ?? "",
                "city" => $data["city"] ?? "",
                "postcode" => $data["postcode"] ?? "",
            ]
        ]);
        return $ret;
    }

    /**
     * @param int $platformId
     * @param int $uid
     * @param string $openid
     * @param bool $isOnlyUpdateSubStatus 是否只更新关注状态,false 同时更新头像昵称等信息
     * @return array
     * 更新微信公众号用户信息, $uid 为bindId,$openid 为对应公众号的openid,传任意一个即可
     */
    public function updateWeChatSubInfo(int $platformId, int $uid, string $openid, bool $isOnlyUpdateSubStatus = false)
    {
        $ret = $this->httpPost(Router::INFO_UPDATE_WECHAT_SUB_INFO, [
            "uid" => $uid,
            "openid" => $openid,
            "platformId" => $platformId,
            "isOnlyUpdateSubStatus" => $isOnlyUpdateSubStatus,
        ]);
        return $ret;
    }

    /**
     * 手机号是否可以绑定检查,会同时更新userinfo json, user_type 作交集检查
     * @param string $telephone
     * @param string $nationCode
     * @param int $userinfoId
     * @param string $origin
     * @return array
     */
    public function BindCheck(string $telephone, string $nationCode, int $userinfoId, string $origin)
    {
        $ret = $this->httpPost(Router::BIND_TELEPHONE_CHECK,
            ["telephone" => $telephone, "nationCode" => $nationCode, "uid" => $userinfoId, "origin" => $origin]);
        return $ret;
    }

    /**
     * 手机号短信验证
     * @param string $telephone
     * @param string $nationCode
     * @param int $userinfoId
     * @param string $code
     * @param string $origin
     * @return array
     */
    public function BindVerify(string $telephone, string $nationCode, int $userinfoId, string $code, string $origin)
    {
        $ret = $this->httpPost(Router::BIND_TELEPHONE_BIND_VERIFY,
            ["telephone" => $telephone, "nationCode" => $nationCode, "uid" => $userinfoId, "code" => $code, "origin" => $origin]);
        return $ret;
    }

    /**
     * 手机号换绑
     * @param string $telephone
     * @param string $nationCode
     * @param int $userinfoId
     * @param string $code
     * @param string $origin
     * @return array
     */
    public function BindChange(string $telephone, string $nationCode, int $userinfoId, string $code, string $origin, string $platform = "")
    {
        $header = Utils::getClientInfo($platform);
        $ret = $this->httpPost(Router::BIND_TELEPHONE_BIND_CHANGE,
            ["telephone" => $telephone, "nationCode" => $nationCode, "uid" => $userinfoId, "code" => $code, "origin" => $origin], $header);
        return $ret;
    }


    /**
     * 小程序手机号自动绑定
     * @param string $telephone
     * @param string $nationCode
     * @param int $userinfoId
     * @param string $origin
     * @return array
     */
    public function BindAutomatic(string $telephone, string $nationCode, int $userinfoId, string $origin, string $platform = "")
    {
        $header = Utils::getClientInfo($platform);
        $ret = $this->httpPost(Router::BIND_TELEPHONE_AUTOMATIC_BIND,
            ["telephone" => $telephone, "nationCode" => $nationCode, "uid" => $userinfoId, "origin" => $origin], $header);
        return $ret;
    }

    /**
     * 通过小程序自动绑定
     * @param string $code
     * @param string $encryptedData
     * @param string $iv
     * @param int $platformId
     * @param string $openId
     * @return array|void
     */
    public function BindPhoneParse(string $code, string $encryptedData, string $iv, int $platformId, int $uid = 0)
    {
        $data = [
            "code" => $code,
            "encryptedData" => $encryptedData,
            "iv" => $iv,
            "platformId" => $platformId,
            "uid" => $uid,
        ];
        if ($error = $this->validate($data, [
            "encryptedData" => "required",
            "iv" => "required",
            "platformId" => "required|int",
        ])) {
            return $error;
        }
        return $this->httpPost(Router::BIND_TELEPHONE_BIND_PHONE_PARSE, $data);
    }

    /**
     * 设置风险用户，天御注册回调使用
     * @param int $accountType
     * @param string $openid
     * @param string $phoneNumber
     * @param int $userinfoId
     * @param int $riskLevel
     * @return array
     */
    public function setRiskUser(int $accountType, string $openid, string $phoneNumber, int $userinfoId, int $riskLevel)
    {
        $data = [
            "accountType" => $accountType,
            "openid" => $openid,
            "phoneNumber" => $phoneNumber,
            "userinfoId" => $userinfoId,
            "riskLevel" => $riskLevel,
        ];
        return $this->httpPost(Router::BASE_SET_RISK_USER, $data);
    }

    /**
     * 获取第一次注册时间
     * @param int $bindId
     * @return array|void
     */
    public function getFirstRegisterTime(int $bindId)
    {
        $data = ["bindId" => $bindId];
        if ($error = $this->validate($data, ["bindId" => "required|int"])) {
            return $error;
        }
        $ret = $this->httpPost(Router::CENTER_GET_LIST_BY_BINDID, [
            'bindid' => $bindId,
            'fields' => ["createTime"],
        ]);
        $this->dealResultData($ret, function ($data) {
            if (empty($data)) {
                return 0;
            }
            $createDataArr = array_column($data, "createTime");
            $createTimeArr = array_map(function ($date) {return strtotime($date);}, $createDataArr);
            return (int)min($createTimeArr);
        });
        return $ret;
    }
}