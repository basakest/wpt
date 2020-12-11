<?php


namespace WptBus\Service\User\Module;


use WptBus\Service\BaseService;
use WptBus\Service\User\Module\Request\Account\GetLogoffListRequest;
use WptBus\Service\User\Router;

/**
 * Class Account
 * @package WptBus\Service\User\Module
 * 账户相关
 */
class Account extends BaseService
{
    /**
     * @param int $uid 当前登录用户
     * @param string $openid 要绑定的微信openid
     * @return array
     */
    public function WeChatChange(int $uid, string $openid)
    {
        $ret = $this->httpPost(Router::WE_CHAT_CHANGE, [
            "uid" => $uid,
            "openid" => $openid
        ]);
        return $ret;
    }

    /**
     * 申请注销
     * @param int $uid
     * @param int $reason 1 不想用了 2 这是多余的账号 3 隐私/安全考虑 4 无法买到心怡拍品 99其它
     * @param string $reasonDesc reason = 99必传
     * @return array
     */
    public function applyLogoff(int $uid, int $reason, string $reasonDesc = "")
    {
        $data = [
            "userinfoId" => $uid,
            "reason" => $reason,
            "reasonDesc" => $reasonDesc
        ];
        if ($error = $this->validate($data, [
            "userinfoId" => "required|int",
            "reason" => "required|int",
            "reasonDesc" => "string",
        ])) {
            return $error;
        }
        return $this->httpPost(Router::APPLY_LOGOUT, $data);
    }

    /**
     * 是否申请注销中
     * @param int $uid
     * @return array
     * {
     * "exist": true,
     * "apply": {}
     * }
     */
    public function isApplyingLogoff(int $uid)
    {
        $data = ["userinfoId" => $uid];
        if ($error = $this->validate($data, ["userinfoId" => "required|int"])) {
            return $error;
        }
        return $this->httpPost(Router::IS_APPLYING_LOGOFF, $data);
    }

    /**
     * @param GetLogoffListRequest $getLogoffListRequest
     * @return array
     *
     * {
     * "total": 100,
     * "list": [{
     * "id": 1,
     * "userinfoId": 1,
     * "reason": 1,
     * "reasonDesc": "",
     * "applyTime": 1599447328,
     * "state": 1,
     * "approverId": 1,
     * "approveTime": 1599447328,
     * "rejectReason": ""
     * }]
     * }
     */
    public function getLogoffList(GetLogoffListRequest $getLogoffListRequest)
    {
        $data = $getLogoffListRequest->toArray();
        return $this->httpPost(Router::GET_LOGOFF_LIST, $data);
    }

    /**
     * 通过注销
     * @param int $id
     * @param string $approverId
     * @return array|void
     */
    public function passLogoff(int $id, string $approverId)
    {
        $data = [
            "id" => $id,
            "approverId" => $approverId,
        ];
        if ($error = $this->validate($data, [
            "id" => "required|int",
            "approverId" => "required|string",
        ])) {
            return $error;
        }
        return $this->httpPost(Router::PASS_LOGOFF, $data);
    }

    /**
     * 驳回注销
     * @param int $id
     * @param string $approverId
     * @param string $rejectReason
     * @return array|void
     */
    public function rejectLogoff(int $id, string $approverId, string $rejectReason)
    {
        $data = [
            "id" => $id,
            "approverId" => $approverId,
            "rejectReason" => $rejectReason,
        ];
        if ($error = $this->validate($data, [
            "id" => "required|int",
            "approverId" => "required|string",
            "rejectReason" => "required|string",
        ])) {
            return $error;
        }
        return $this->httpPost(Router::REJECT_LOGOFF, $data);

    }

    /**
     * 账户列表
     * @param int $uid
     * @return array
     */
    public function accountList(int $uid)
    {
        $data = ["uid" => $uid];
        if ($error = $this->validate($data, ["uid" => "required|int"])) {
            return $error;
        }
        return $this->httpPost(Router::ACCOUNT_LIST, $data);
    }

    /**
     * 账户详情
     * @param string $identity
     * @return array
     */
    public function accountDetail(string $identity)
    {
        $data = ["identity" => $identity];
        if ($error = $this->validate($data, ["identity" => "required|string"])) {
            return $error;
        }
        return $this->httpPost(Router::ACCOUNT_DETAIL, $data);
    }

}