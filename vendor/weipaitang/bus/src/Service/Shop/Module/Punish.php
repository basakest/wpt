<?php


namespace WptBus\Service\Shop\Module;

use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Service\BaseService;
use WptBus\Service\Shop\Router;

class Punish extends BaseService
{
    const FORBIDDEN_SHOP = "ForbiddenShop";  //封店

    const DELAY_PAYMENT = "DelayAccountPayment"; //延长账期

    const REDUCE = "Reduce"; //分类降权

    const NEW_REDUCE = "NewReduce"; //新的分类降权

    const REDUCE16 = "Reduce16"; // 分类降权16

    const FORBIDDEN_PUBLISH = "ForbiddenPublish"; //禁止上拍

    const FORBIDDEN_BAIL = "ForbiddenBailUser"; //禁提消保金

    const FORBIDDEN_SCALPING = "Scalping"; //刷单

    const FORBIDDEN_WITHDRAW = "ForbiddenWithdraw"; //禁止提现

    const FORBIDDEN_SHOP_BAIL = "ForbiddenShopBail";  //店铺保证金封店


    // 每种处罚的执行参数
    protected $punishParams = [
        self::FORBIDDEN_SHOP => [
            "days" => 0,      //封店天数
            "bails" => 0      //保证金
        ],
        self::DELAY_PAYMENT => [
            "days" => 0,     //处罚天数
            "duration" => 0, //持续时间
        ],
        self::REDUCE => [
            "reduce" => 7
        ],
        self::NEW_REDUCE => [
            "score" => 17,
            "days" => 7
        ],
        self::REDUCE16 => [
            "score" => 16
        ],
        self::FORBIDDEN_PUBLISH => [
            "days" => 0    //禁止上拍天数
        ],
        self::FORBIDDEN_BAIL => [
            "days" => 0    //禁提消保金天数
        ],
        self::FORBIDDEN_SCALPING => [
            "days" => 0     //刷单
        ],
        self::FORBIDDEN_WITHDRAW => [
            "days" => 0     //禁止提现
        ]
    ];

    // 即将执行的处罚类型
    private $punishTypes = [];
    // 被执行处罚的对象
    private $targetUser;


    /**
     * 被执行处罚的用户
     * @param int $userId
     * @return $this
     */
    public function setTargetUser(int $userId)
    {
        $this->targetUser = $userId;
        return $this;
    }

    /**
     * 处罚类型
     * @param array $types
     * @return $this
     */
    public function setPunishTypes(array $types)
    {
        $this->punishTypes = $types;
        return $this;
    }

    /**
     * 设置封店参数
     * @param int $days
     * @param int $bails
     * @return $this
     */
    public function setForbiddenShop(int $days, int $bails = 0)
    {
        $this->punishParams[self::FORBIDDEN_SHOP]["days"] = $days;
        $this->punishParams[self::FORBIDDEN_SHOP]["bails"] = $bails;
        return $this;
    }

    /**
     * 设置延迟处罚参数
     * @param int $day 每笔交易延长账期的时间
     * @param int $duration 处罚过期时间
     * @return $this
     */
    public function setDelayPayment(int $day, int $duration)
    {
        $this->punishParams[self::DELAY_PAYMENT]["days"] = $day;
        $this->punishParams[self::DELAY_PAYMENT]["duration"] = $duration;
        return $this;
    }

    /**
     * 设置禁止时间长度
     * @param int $day
     * @return $this
     */
    public function setForbiddenPublish(int $day)
    {
        $this->punishParams[self::FORBIDDEN_PUBLISH]["days"] = $day;
        return $this;
    }

    /**
     * 设置用户禁止提取消保金
     * @param int $day
     * @return $this
     */
    public function setForbiddenUserBail(int $day)
    {
        $this->punishParams[self::FORBIDDEN_BAIL]["days"] = $day;
        return $this;
    }

    /**
     * 设置刷单处罚
     * @param int $days
     * @return $this
     */
    public function setForbiddenScalping(int $days)
    {
        $this->punishParams[self::FORBIDDEN_SCALPING]["days"] = $days;
        return $this;
    }

    /**
     * 设置禁止提现
     * @param int $days
     * @return $this
     */
    public function setForbiddenWithdraw(int $days)
    {
        $this->punishParams[self::FORBIDDEN_WITHDRAW]["days"] = $days;
        return $this;
    }

    /**
     * 执行处罚的参数
     * @return array
     */
    public function generateParams()
    {
        return ['punishSets' => $this->validAndGetPunish(), 'uid' => $this->getTargetUser()];
    }

    /**
     * 效验处罚类型不能为空
     * @return array
     */
    public function vaildPunishTypes()
    {
        return $this->punishTypes;
    }

    /**
     * 效验和获取设置的处罚类型
     * @return array
     */
    public function validAndGetPunish()
    {
        $types = $this->vaildPunishTypes();
        $punish = [];
        foreach ($types as $type) {
            $punish[$type] = $this->punishParams[$type];
        }
        return $punish;
    }

    /**
     * 获取目标用户id
     * @return int|string
     */
    public function getTargetUser()
    {
        return $this->targetUser;
    }


    /**
     * 执行对用户的处罚（迁移user-service sdk）
     * @api {post} UCPunish::getInstance()
     * ->setPunishTypes([UCPunish::FORBIDDEN_SHOP,UCPunish::DELAY_PAYMENT])
     * ->setForbiddenShop($days)->setDelayPayment($day,$duration)
     * ->setTargetUser($userId)->execPunish();  执行处罚
     *
     * @apiParam {String} UCPunish::FORBIDDEN_SHOP 封店类型
     * @apiParam {String} UCPunish::DELAY_PAYMENT 延长账期类型
     * @apiParam {String} UCPunish::REDUCE 降权
     * @apiParam {String} UCPunish::FORBIDDEN_PUBLISH 禁止上拍
     * @apiParam {String} UCPunish::FORBIDDEN_BAIL 禁提消保金
     * @apiParam {String} UCPunish::FORBIDDEN_SCALPING 刷单
     * @apiParam {String} UCPunish::ForbiddenWithdraw 禁止提现
     *
     * @apiSuccessExample {json} Request:
     * {
     *          "step1": "第一步：设置要执行的处罚类型
     * setPunishTypes([UCPunish::FORBIDDEN_SHOP,UCPunish::DELAY_PAYMENT])
     *          "step2": "第二步：设置对应处罚类型的执行参数，如：封店处罚， setForbiddenShop($days)
     *          "step3": "第三步：设置被处罚的用户  setTargetUser($userId)
     *          "step4": "第四步：执行处罚 execPunish($http)
     * }
     *
     * @return array
     *
     * @apiSuccessExample {json} Success-Response:
     * {
     *      "code":0, //状态码
     *      "msg":"",
     *      "data":{
     *              "ForbiddenShop":true,
     *              "DelayAccountPayment":true
     *      }
     * }
     */
    public function execPunish()
    {
        $params = $this->generateParams();

        return $this->httpPost(Router::EXEC_PUNISH, $params);
    }

    /**
     * 查询用户被执行的处罚的类型（迁移user-service sdk）
     * @param int $shopId
     * @param array $punishTypes
     * @return array
     */
    public function inquirePunish(int $shopId, array $punishTypes)
    {
        $params = ['uid' => $shopId, 'types' => $punishTypes];

        return $this->httpPost(Router::INQUIRE_PUNISH, $params);
    }

    /**
     * 取消用户的处罚（迁移user-service sdk）
     * @param int $shopId
     * @param array $punishTypes
     * @return array
     */
    public function cancelPunish(int $shopId, array $punishTypes)
    {
        $params = ["uid" => $shopId, "types" => $punishTypes];

        return $this->httpPost(Router::CANCEL_PUNISH, $params);
    }

    /**
     * 过滤用户的处罚（迁移user-service sdk）
     * @param int $shopId
     * @param array $punishTypes
     * @return array
     */
    public function filter(int $shopId, array $punishTypes)
    {
        $params = ["uid" => $shopId, "types" => $punishTypes];

        return $this->httpPost(Router::FILTER_PUNISH, $params);
    }


    /**
     * 查询某个惩罚类型的所有用户（迁移user-service sdk）
     * @param $punishType
     * @param bool $exist
     * @return array
     */
    public function inquireAllPunish($punishType, $exist = true)
    {
        $params = [ "type" => $punishType];
        $res = $this->httpPost(Router::GET_ALL_PUNISH_BY_TYPE, $params);

        $result = [];
        if (count($res) > 0 && isset($res['code'])
            && $res['code'] == 0 && isset($res['data'])) {
            $data = $res['data'];
            foreach ($data as $k => $v) {
                if ($exist && $v['exist']) {
                    $result[$k] = $v["value"];
                }
                if (!$exist) {
                    $result[$k] = $v["value"];
                }
            }
        }
        return $result;
    }

    /**
     * 获取惩罚信息
     * @param int $shopId
     * @param array $punishTypes
     * [
     * "ForbiddenShop"  封店
     * "ForbiddenPublish" 禁止上拍
     * "DelayAccountPayment" 延长账期
     * "ForbiddenBailUser" 禁提消保金
     * "ForbiddenWithdraw" 禁止提现
     * "Reduce" 降权
     * "Scalping" 刷单
     * "NewReduce" 新的降权17分
     * "Reduce16" 降权16分
     * "ForbiddenShopBail" 店铺保证金封店
     * ]
     * @return array
     * 永久惩罚例子：
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1602739381,
     * "data": {
     * "ForbiddenShop": { 惩罚类型
     * "exist": true,   是否惩罚中
     * "value": -1,     -1永久惩罚 其他值是对应惩罚过期时间戳
     * "deadline": "永久" 惩罚过期时间描述
     * }
     * }
     * }
     * 带有过期时间的惩罚例子
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1602740658,
     * "data": {
     * "ForbiddenShop": {
     * "exist": true,
     * "value": 1602826382,
     * "deadline": "2020-10-16 13:33:02"
     * }
     * }
     * }
     *
     */
    public function getPunishInfo(int $shopId, array $punishTypes)
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId错误");
        }
        if (empty($punishTypes)) {
            return Response::byBus(Error::INVALID_ARGUMENT, "参数punishTypes必须");
        }

        $parame = [
            'uid' => $shopId,
            'types' => $punishTypes,
        ];

        return $this->httpPost(Router::GET_SHOP_PUNISH_INFO, $parame);
    }


    /**
     * 惩罚---设置店铺发拍限制数
     * @param int $shopId             店铺ID
     * @param int $limitPublishNum    发拍限制数
     * @param int $expireTime         数据有效过期时间
     * @return array
     *
     * 正常示例
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1603339072,
     * "data": {
     * "isOk": true
     * }
     * }
     *
     * 异常错误示例
     * {
     * "code": 202029,
     * "msg": "shopId错误，应大于0",
     * "nowTime": 1603348773,
     * "data": null
     * }
     *
     *
     */
    public function setShopLimitPublishNum(int $shopId, int $limitPublishNum, int $expireTime)
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId错误，应大于0");
        }
        if ($limitPublishNum < 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "limitPublishNum错误，应大于等于0");
        }
        if ($expireTime <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "expireTime错误，应大于0");
        }

        $parame = [
            'shopId' => $shopId,
            'limitPublishNum' => $limitPublishNum,
            'expireTime' => $expireTime,
        ];

        return $this->httpPost(Router::SET_SHOP_LIMIT_PUBLISH_NUM, $parame);
    }


    /**
     * 根据惩罚类型分页获取店铺未过期的惩罚信息列表（优化Hgetall方式）
     * @param int $punishType
     * 1延长账期; 2封店; 3禁提消保金; 4禁止提现; 5标记店铺刷单; 6禁止发布拍品; 7降权; 8新的降权17分; 9降权16分
     * @param string $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     * 返回数据示例：
     * {
     * "code": 0,
     * "msg": "",
     * "nowTime": 1603849591,
     * "data": [
     * {
     * "shopId": 8610066,
     * "expiredTime": 2147483647
     * },
     * {
     * "shopId": 200,
     * "expiredTime": 2147483647
     * },
     * {
     * "shopId": 8611161,
     * "expiredTime": -1
     * }
     * ]
     * }
     *
     * 无数据示例：
     *
     *{
     * "code": 0,
     * "msg": "",
     * "nowTime": 1603849672,
     * "data": []
     * }
     *
     *
     */
    public function getShopPunishListByType(int $punishType, $orderBy = "", $limit = 100, $offset = 0)
    {
        if ($punishType <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "punishType应大于0");
        }
        if ($offset < 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "offset应大于等于0");
        }
        if ($limit <= 0 || $limit > 200) {
            return Response::byBus(Error::INVALID_ARGUMENT, "limit应为1～200");
        }

        $parame = [
            'punishType' => (int)$punishType,
            'orderBy' => (string)$orderBy,
            'offset' => (int)$offset,
            'limit' => (int)$limit,
        ];

        return $this->httpPost(Router::GET_SHOP_PUNISH_INFO_LIST_BY_TYPE, $parame);
    }


}