<?php


namespace WptBus\Service\User\Module;


use Illuminate\Validation\Rule;
use WptBus\Lib\Utils;
use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class UserVerify extends BaseService
{
    const UPDATE_USER_TELEPHONE_VERIFY_INFO_TYPE = [
        "clear" => 0,
        "sms" => 1,
        "call" => 2
    ];

    /**
     * 手机验证
     * @param $uid
     * @param $wholePhone
     * @param $verifyType
     * @return array
     */
    public function telephoneVerify(int $uid, string $wholePhone, int $verifyType)
    {
        $data = [
            "uid" => $uid,
            "wholeMobile" => $wholePhone,
            "verifyType" => $verifyType
        ];
        return $this->httpPost(Router::INFO_UPDATE_USER_TELEPHONE_VERIFY_INFO, $data);
    }

    /**
     * 手机号绑定验证
     * @param int $uid
     * @param string $wholeTel
     * @return array
     */
    public function telBindVerify(int $uid, string $wholeTel)
    {
        $data = ["uid" => $uid, "targetTelephone" => $wholeTel];
        return $this->httpPost(Router::MERGE_TELEPHONE_CHECK, $data);
    }

    /**
     * 手机号绑定
     * @param int $uid
     * @param string $wholeTel
     * @param bool $isNewProcess
     * @param string $platform
     * @return array
     */
    public function telBind(int $uid, string $wholeTel, bool $isNewProcess = true, string $platform = "")
    {
        $header = Utils::getClientInfo($platform);
        $data = ["uid" => $uid, "targetTelephone" => $wholeTel, "isNewProcess" => $isNewProcess];
        return $this->httpPost(Router::MERGE_TELEPHONE, $data, $header);
    }

    /**
     * 微信绑定验证
     * @param $bindId
     * @return array
     */
    public function wxBindVerify(int $uid, int $targetUid)
    {
        $data = ["uid" => $uid, "targetUid" => $targetUid];
        return $this->httpPost(Router::MERGE_WE_CHAT_CHECK, $data);
    }

    /**
     * 微信绑定
     * @param $uid
     * @param $verifyUid
     * @return array
     */
    public function wxBind(int $uid, int $targetUid)
    {
        $data = ["uid" => $uid, "targetUid" => $targetUid];
        return $this->httpPost(Router::MERGE_WE_CHAT, $data);
    }

    /**
     * 微信解绑
     * @param $uid
     * @return array
     */
    public function wxUnBind(int $uid)
    {
        $data = ["uid" => $uid];
        return $this->httpPost(Router::MERGE_WE_CHAT_UN_MERGE, $data);
    }

    /**
     * 绑定手机号解析 通过 阿里一键或阿里一键验证
     * @param string $code
     * @param int $platformId
     * @param string $telephone
     * @return array|void
     */
    public function bindPhoneParseByOneClick(string $code, int $platformId, string $telephone = "")
    {
        $data = [
            "code" => $code,
            "platformId" => $platformId,
            "telephone" => $telephone
        ];
        if ($error = $this->validate($data, [
            "code" => "required",
            "platformId" => "required|int",
        ])) {
            return $error;
        }
        return $this->httpPost(Router::BIND_TELEPHONE_BIND_PHONE_PARSE_BY_ONE_CLICK, $data);
    }

    /**
     * 更新身份证信息
     * @param int $uid
     * @param $name
     * @param $idCard
     * @return array|void
     */
    public function updateIdCardInfo(int $uid, string $name, string $idCard)
    {
        $data = ["uid" => $uid, "name" => $name, "idCode" => $idCard];
        if ($error = $this->validate($data,
            ["uid" => "required|int", "name" => "required|string", "idCode" => "required|string"])) {
            return $error;
        }
        return $this->httpPost(Router::INFO_UPDATE_ID_CARD_INFO, $data);
    }

    // 活体校验方式
    const LIVING_VERIFY_WAY = [
        "wechat" => "wechat",
        "sms" => "sms",
    ];

    /**
     * 创建活体校验码
     * @param int $uid
     * @param string $way
     * @return array|void
     *
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 0,
     * "data": {
     * "code": "8690",
     * "expire": 180
     * }
     * }
     */
    public function createLivingVerifyCode(int $uid, string $way)
    {
        $data = ["uid" => $uid, "way" => $way];
        if ($error = $this->validate($data,
            ["uid" => "required|int", "way" => ["required", Rule::in(self::LIVING_VERIFY_WAY)]])) {
            return $error;
        }
        return $this->httpPost(Router::USER_VERIFY_CREATE_LIVING_VERIFY_CODE, $data);
    }

    /**
     * 获取活体校验码
     * @param int $uid
     * @param string $way
     * @return array|void 返回同 创建活体校验码
     */
    public function getLivingVerifyCode(int $uid, string $way)
    {
        $data = ["uid" => $uid, "way" => $way];
        if ($error = $this->validate($data,
            ["uid" => "required|int", "way" => ["required", Rule::in(self::LIVING_VERIFY_WAY)]])) {
            return $error;
        }
        return $this->httpPost(Router::USER_VERIFY_GET_LIVING_VERIFY_CODE, $data);
    }

    /**
     * 校验活体校验码
     * @param int $uid
     * @param string $way
     * @return array|void
     *
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 0,
     * "data": true
     * }
     */
    public function checkLivingVerifyCode(int $uid, string $way, string $code)
    {
        $data = ["uid" => $uid, "way" => $way, "code" => $code];
        if ($error = $this->validate($data,
            ["uid" => "required|int", "way" => ["required", Rule::in(self::LIVING_VERIFY_WAY)], "code" => "required|string"])) {
            return $error;
        }
        return $this->httpPost(Router::USER_VERIFY_CHECK_LIVING_VERIFY_CODE, $data);
    }

    /**
     * 获取活体校验信息
     * @param int $uid
     * @return mixed
     *
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 0,
     * "data": {
     * "verifyWay": 7,
     * "verifyAt": 1595907807,
     * "verifyTypeDesc": "活体",
     * "verifyWayDesc": "公众号校验"
     * }
     * }
     */
    public function getLivingVerifyInfo(int $uid)
    {
        $ret = $this->getVerifyList($uid, self::LIVING_VERIFY_WAYS_LIST);
        $this->dealResultData($ret, function ($data) {
            return $data ? $data[0] : [];
        });
        return $ret;
    }

    // 认证方式
    const VerifyWayTelephoneVerifySms = 1;
    const VerifyWayTelephoneVerifyCall = 2;
    const VerifyWayLivingVerifySms = 7;
    const VerifyWayLivingVerifyWeChat = 8;
    const VerifyWayHolderVerifyBankCard = 9;
    const VerifyWayHolderVerifyIdCard = 10;
    const VerifyWayHolderVerifyPurchaseHistory = 11;
    const VerifyWayHolderVerifyVerify3Meta = 12;

    // 手机号认证方式
    const TELEPHONE_VERIFY_WAYS_LIST = [
        self::VerifyWayTelephoneVerifySms, // 短信
        self::VerifyWayTelephoneVerifyCall, // 语音
    ];

    // 活动认证方式列表
    const LIVING_VERIFY_WAYS_LIST = [
        self::VerifyWayLivingVerifySms, // 短信
        self::VerifyWayLivingVerifyWeChat, // 公众号
    ];
    // 持有人校验方式列表
    const HOLDER_VERIFY_WAYS_LIST = [
        self::VerifyWayHolderVerifyBankCard, // 银行卡
        self::VerifyWayHolderVerifyIdCard, // 身份证
        self::VerifyWayHolderVerifyPurchaseHistory, // 购买记录
        self::VerifyWayHolderVerifyVerify3Meta, // 三要素
    ];
    // 安全认证方式列表（后台用）
    const SAFETY_VERIFY_WAYS_LIST = [
        self::VerifyWayLivingVerifySms,
        self::VerifyWayLivingVerifyWeChat,
        self::VerifyWayHolderVerifyBankCard,
        self::VerifyWayHolderVerifyIdCard,
        self::VerifyWayHolderVerifyPurchaseHistory,
        self::VerifyWayHolderVerifyVerify3Meta,
    ];

    /**
     * 获取认证列表
     * @param int $uid
     * @param array $verifyWays
     * @return array|void
     *
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 0,
     * "data": [
     * {
     * "verifyWay": 7,
     * "verifyAt": 1595907807,
     * }
     * ]
     * }
     *
     */
    public function getVerifyList(int $uid, array $verifyWays)
    {
        $data = ["uid" => $uid, "verifyWays" => $verifyWays];
        if ($error = $this->validate($data,
            ["uid" => "required|int", "verifyWays" => "required|array"])) {
            return $error;
        }
        return $this->httpPost(Router::USER_VERIFY_GET_VERIFY_INFO_LIST, $data);
    }

    const HOLDER_VERIFY_WAY_SCENE = [
        "logout" => "logout", // 注销
        "remoteLogin" => "remoteLogin" // 异地登录
    ];

    /**
     * 获取持有人校验方式
     * @param int $uid
     * @param string $scene
     * @return array|void
     *
    {
    "telVerifyStatus": 0, // 0:不需要校验或校验成功 1:需要验证手机号  2:需要绑定手机号
    "wayList": ["bankCard", "idCard", "purchaseHistory", "verify3Meta"]
    }
     */
    public function getHolderVerifyWay(int $uid, string $scene)
    {
        $data = ["uid" => $uid, "scene" => $scene];
        if ($error = $this->validate($data,
            ["uid" => "required|int", "scene" => ["required", Rule::in(self::HOLDER_VERIFY_WAY_SCENE)]])) {
            return $error;
        }
        return $this->httpPost(Router::USER_VERIFY_GET_HOLDER_VERIFY_WAY, $data);
    }

    /**
     * 获取持有人校验方式数据
     * @param int $uid
     * @param string $way
     * @param bool $onlyRemainTimes 是否只返回剩余次数
     * @return array|void
     *
    {
    "remainTimes": 2, // 剩余次数
    "data": {} // data根据way返回特定信息，具体如下
    }

    idCard:
    {
    "list": [{
    "id": 1,
    "bankName": "工商银行"
    }]
    }

    purchaseHistory:
    {
    "list": [{
    "id": 1,
    "img": "https://cdn.weipaitang.com/static/20200805413500af-12a1-00af12a1-9c72-1ba80385f34d-W563H233/w/640"
    }]
    }

    verify3Meta:
    {
    "telephone": "18768495142"
    }
     */
    public function getHolderVerifyWayData(int $uid, string $way, bool $onlyRemainTimes = false)
    {
        $data = ["uid" => $uid, "way" => $way, "onlyRemainTimes" => $onlyRemainTimes];
        if ($error = $this->validate($data,
            ["uid" => "required|int", "way" => "required|string"])) {
            return $error;
        }
        $ret = $this->httpPost(Router::USER_VERIFY_GET_HOLDER_VERIFY_WAY_DATA, $data);
        $this->dealResultData($ret, function ($data) {
            if ($data && !empty($data["data"])) {
                $data["data"] = json_decode($data["data"], true);
                return $data;
            }
            return $data;
        });
        return $ret;
    }

    /**
     * 持有人校验
     * @param int $uid
     * @param string $scene
     * @param string $way
     * @param array $data
     *
    bankCard:
    {
    "id": 1,
    "tailNumber": "5142"
    }

    idCard:
    {
    "tailNumber": "5142"
    }

    purchaseHistory:
    {
    "idList": [1, 2, 3]
    }

    verify3Meta:
    {
    "name": "123",
    "idCode": "1234"
    }
     *
     * @return array|void
    {
    "result": true, // 认证结果
    "remainTimes": 1 // 剩余次数
    }
     */
    public function checkHolderVerify(int $uid, string $scene, string $way, array $data)
    {
        $data = json_encode($data);
        $data = ["uid" => $uid, "scene" => $scene, "way" =>$way , "data" => $data];
        if ($error = $this->validate($data,
            ["uid" => "required|int",
                "scene" => ["required", Rule::in(self::HOLDER_VERIFY_WAY_SCENE)],
                "way" => "required|string",
                "data" => "required|string"
            ])) {
            return $error;
        }
        return $this->httpPost(Router::USER_VERIFY_CHECK_HOLDER_VERIFY, $data);
    }

    /**
     * 手动一键校验
     * @param int $uid
     * @param string $telephone
     * @return array|void
     */
    public function manualOneClickVerify(int $uid, string $telephone)
    {
        $parame = [
            'userinfoId' => (int)$uid,
            'telephone' => (string)$telephone,
        ];
        return $this->httpPost(Router::MANUAL_ONE_CLICK_VERIFY, $parame);
    }
}