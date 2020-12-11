<?php


namespace WptBus\Service\User\Module;


use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class Wechat extends BaseService
{
    const WECHAT_TYPE_LIST = [2, 3, 4, 9, 12, 13, 24, 27];

    /**
     * 获取微信账号列表
     * @param $uid
     * @param array $field
     * @return array|void
     */
    public function getList(int $bindId, $field = [])
    {
        if ($error = $this->validate(["bindId" => $bindId], ["bindId" => "required|int"])) {
            return $error;
        }
        $ret = $this->httpPost(Router::CENTER_GET_LIST_BY_BINDID, [
            'bindid' => $bindId,
            'fields' => in_array("userType", $field) ? $field : array_merge($field, ["userType"]),
        ]);
        $this->dealResultData($ret, function ($data) {
            $wechatList = [];
            foreach ($data as $k => $v) {
                if (in_array($v["userType"], self::WECHAT_TYPE_LIST)) {
                    $wechatList[] = $v;
                }
            }
            return $wechatList;
        });
        return $ret;
    }
}