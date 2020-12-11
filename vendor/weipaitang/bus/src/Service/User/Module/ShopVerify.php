<?php

namespace WptBus\Service\User\Module;

use WptBus\Lib\Utils;
use App\ConstDir\ErrorConst;
use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Service\BaseService;
use WptBus\Service\User\Module\Request\SaveEnterpriseCertificationInfoRequest;
use WptBus\Service\User\Module\Request\SaveShopInfoRequest;
use WptBus\Service\User\Router;
use WptBus\Service\User\Module\Request\SavePersonalAuthenticationInfoRequest;

class ShopVerify extends BaseService
{
    //检查店铺名
    public function CheckShopName(int $uid, string $name)
    {
        $ret = $this->httpPost(Router::CHECK_SHOP_NAME, [
            "uid"=>$uid,
            "name"=>$name
        ]);
        return $ret;
    }

    //头像,简介
    public function SaveShopInfo(SaveShopInfoRequest $request)
    {
        $ret = $this->httpPost(Router::SAVE_SHOP_INFO, $request->toArray());
        return $ret;
    }
    //个人认证信息提交
    public function SavePersonalAuthenticationInfo(SavePersonalAuthenticationInfoRequest $request)
    {
        $ret = $this->httpPost(Router::SAVE_PERSONAL_AUTHENTICATION_INFO, $request->toArray());
        return $ret;
    }

    //企业信息提交
    public function SaveEnterpriseCertificationInfo(SaveEnterpriseCertificationInfoRequest $request)
    {
        $ret = $this->httpPost(Router::SAVE_ENTERPRISE_CERTIFICATION_INFO, $request->toArray());
        return $ret;
    }


    /**
     * 查询我的认证申请信息
     * @param int $userinfoId
     * @return array
     */
    public function GetVerifyDetail(int $userinfoId)
    {

        $parame = [
            "userinfoId" => (int)$userinfoId,
        ];
        $ret = $this->httpPost(Router::GET_VERIFY_DETAIL, $parame);
        if (isset($ret["data"]) && !empty($ret["data"])) {
            // 将字符类型转为bool
            if ($ret['data']['shop']['modifiable'] === "1") {
                $ret['data']['shop']['modifiable'] = true;
            } else {
                $ret['data']['shop']['modifiable'] = false;
            }
            // 转换类型
            if(isset($ret['data']['shop']['isFreeVerify'])){
                $ret['data']['shop']['isFreeVerify'] = (int)$ret['data']['shop']['isFreeVerify'];
            }
            if(isset($ret['data']['shop']['isExFreeVerify'])){
                $ret['data']['shop']['isExFreeVerify'] = (int)$ret['data']['shop']['isExFreeVerify'];
            }
        }
        return $ret;
    }


    /**
     * 认证草稿信息
     * @param int $userinfoId
     * @return array
     */
    public function GetApplyInfo(int $userinfoId)
    {

        $parame = [
            "userinfoId" => (int)$userinfoId,
        ];
        $ret = $this->httpPost(Router::GET_APPLY_INFO, $parame);
        if (isset($ret["data"]) && !empty($ret["data"])) {

            $draftErrJson = "{}";
            if (isset($ret['data']['rebutMsg']) && !empty($ret['data']['rebutMsg'])) {
                $draftErrJson = $ret['data']['rebutMsg'];
            }
            $ret['data']['error'] = json_decode($draftErrJson);

            // 转换类型
            if(isset($ret['data']['shop']['isFreeVerify'])){
                $ret['data']['shop']['isFreeVerify'] = (int)$ret['data']['shop']['isFreeVerify'];
            }
            if(isset($ret['data']['shop']['isExFreeVerify'])){
                $ret['data']['shop']['isExFreeVerify'] = (int)$ret['data']['shop']['isExFreeVerify'];
            }
        }

        return $ret;
    }


    /**
     * 检查认证申请信息
     * @param int $userinfoId
     * @param string $verifyType
     * @return array
     */
    public function ReviewApplyInfo(int $userinfoId, string $verifyType)
    {

        $parame = [
            "userinfoId" => (int)$userinfoId,
            "verifyType" => (string)$verifyType,
        ];
        $ret = $this->httpPost(Router::REVIEW_APPLY_INFO, $parame);
        return $ret;
    }

    /**
     * 初始化支付
     * @param int $userinfoId
     * @param string $verifyType
     * @return array
     */
    public function ToPayVerifyInfo(int $userinfoId, string $verifyType)
    {
        $parame = [
            "userinfoId" => (int)$userinfoId,
            "verifyType" => (string)$verifyType,
        ];
        $ret = $this->httpPost(Router::TO_PAY_VERIFY_INFO, $parame);
        return $ret;
    }


    /**
     * 支付完成回调更新信息，删除codis
     * @param int $userinfoId
     * @param string $info
     * @return array
     */
    public function CallBackDelCodis(int $userinfoId, string $info)
    {
        $parame = [
            "userinfoId" => (int)$userinfoId,
            "info" => (string)$info,
        ];
        $ret = $this->httpPost(Router::CALL_BACK_DEL_CODIS, $parame);
        return $ret;
    }


    /**
     * 获取认证信息（并检查用户是否存在售假标签）
     * @param int $userinfoId
     * @param string $verifyType
     * @return array
     */
    public function GetVerifyInfoAndCheckIsFake(int $userinfoId, string $verifyType)
    {
        $parame = [
            "userinfoId" => (int)$userinfoId,
            "verifyType" => (string)$verifyType,
        ];

        $ret = $this->httpPost(Router::GET_VERIFY_CHECK_IS_FAKE, $parame);

        return $ret;
    }


    /**
     * 更新状态到审核中并增加到征信队列
     * @param int $userinfoId
     * @param string $protectedName
     * @param string $verifyType
     * @param string $orderJson
     * @return array
     */
    public function UpdateVerifyStatusAndRpushZx(int $userinfoId, string $verifyType, string $protectedName = "", string $orderJson = "")
    {

        $parame = [
            "userinfoId" => (int)$userinfoId,
            "protectedName" => (string)$protectedName,
            "verifyType" => (string)$verifyType,
            "orderJson" => (string)$orderJson,
        ];

        $ret = $this->httpPost(Router::UPDATE_VERIFY_RPUSH_ZX, $parame);

        return $ret;
    }

    /**
     * 认证数据记录列表
     * @param $userinfoId
     * @param array $columns
     * @param string $queryWhere 数组形式转成的字符串
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function GetUserinfoVerifyLogList($userinfoId, $columns = ['*'], $queryWhere = '', $order = 'id desc', $limit = 50, $offset = 0)
    {
        $parame = [
            'userinfoId' => (int)$userinfoId,
            'columns' => (array)$columns,
            'queryWhere' =>(string)$queryWhere,
            'order' => (string)$order,
            'limit' => (int)$limit,
            'offset' => (int)$offset,
        ];

        $ret = $this->httpPost(Router::GET_USERINFO_VERIFY_LOG_LIST, $parame);

        return $ret;
    }

    /**
     * 新增首次认证数据
     * @param $userinfoId
     * @param $isOldUser
     * @param $firstVerifyTime
     * @param $createTime
     * @param $updateTime
     * @return array
     */
    public function InsertTUserVerify($userinfoId, $isOldUser, $firstVerifyTime, $createTime, $updateTime)
    {
        $parame = [
            'userinfoId' => (int)$userinfoId,
            'isOldUser' => (int)$isOldUser,
            'firstVerifyTime' =>(int)$firstVerifyTime,
            'createTime' => (int)$createTime,
            'updateTime' => (int)$updateTime,
        ];

        $ret = $this->httpPost(Router::INSERT_T_USER_VERIFY_INFO, $parame);

        return $ret;
    }

    /**
     * 获取首次认证时间
     * 由于历史数据问题 若isOldUser=1老用户 则firstVerifyTime时间可能不准，请使用方斟酌判断
     * @param $userinfoId
     * @param $fields
     * @return array
     */
    public function GetTUserVerify($userinfoId, $fields)
    {
        $parame = [
            'userinfoId' => (int)$userinfoId,
            'fields' => (array)$fields,
        ];

        $ret = $this->httpPost(Router::GET_T_USER_VERIFY_INFO, $parame);

        return $ret;
    }


    /**
     * 认证 涉政售假校验
     * @param int $shopId
     * @param string $verifyType
     * @param string $idCode
     * @param string $businessName
     * @param bool $isPaySource
     * @return array
     */
    public function CheckVerifyIsPoliticsFaker(int $shopId, string $verifyType, string $idCode, string $businessName, bool $isPaySource)
    {
        if ($shopId <= 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "shopId 必须");
        }
        if ($verifyType == "") {
            return Response::byBus(Error::INVALID_ARGUMENT, "verifyType 必须");
        }
        if (!$isPaySource && $idCode == "") {
            return Response::byBus(Error::INVALID_ARGUMENT, "idCode 必须");
        }

        $parame = [
            'shopId' => $shopId,
            'verifyType' => $verifyType,
            'idCode' => $idCode,
            'businessName' => $businessName,
            'isPaySource' => $isPaySource,
        ];
        $ret = $this->httpPost(Router::CHECK_VERIFY_IS_POLITICS_FAKER, $parame);

        return $ret;
    }

    /**
     * 获取认证信息列表
     * @param array $fields
     * @param string $queryWhere
     * @param array $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function GetUserinfoVerifyList(array $fields, string $queryWhere, array $order = [], $limit = 50, $offset = 0)
    {
        if (count($fields) == 0) {
            return Response::byBus(Error::INVALID_ARGUMENT, "fields 必须");
        }
        $parame = [
            'fields' => $fields,
            'where' => $queryWhere,
            'order' => $order,
            'limit' => $limit,
            'offset' => $offset,
        ];

        $ret = $this->httpPost(Router::GET_USERINFO_VERIFY_LIST, $parame);

        return $ret;
    }

    /**
     * 获取认证信息（迁移user-service sdk）
     * @param int $userId
     * @return mixed
     */
    public function getUserVerifyInfo(int $userId)
    {
        $params = ["uid" => $userId];
        $res = $this->httpPost(Router::GET_SHOP_VERIFY_INFO, $params);
        $result = json_decode(json_encode($res));
        $data = Utils::property($result, "data", new \stdClass());
        $draftErrJson = Utils::property($data, "draftErrorJson", "{}");
        $data->draftError = json_decode($draftErrJson);
        $result->data = $data;
        return $result;
    }

    /**
     * 更新认证状态（迁移user-service sdk）
     * @param int $userId
     * @param $verifyType
     * @param string $protectedName
     * @param string $orderJson
     * @return array
     */
    public function verifyStatusToReview(int $userId, $verifyType, $protectedName = "", $orderJson = "")
    {
        $params = ["uid" => $userId, "protectedShopName" => $protectedName, "orderJson" => $orderJson, "verifyType" => $verifyType];

        return $this->httpPost(Router::VERIFY_STATUS_TO_REVIEW, $params);
    }

    /**
     * 更新认证信息（迁移user-service sdk）
     * @param int $userId
     * @param $info
     * @return array
     */
    public function updateVerifyInfo(int $userId, $info)
    {
        $params = ["uid" => $userId, "info" => json_encode($info)];

        return $this->httpPost(Router::UPDATE_VERIFY_INFO, $params);
    }


}
