<?php


namespace WptBus\Service\User\Module;


use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class UserRelation extends BaseService
{
    /**
     * 全站拉黑
     * @param int $uid
     * @return array|void
     */
    public function isBlack(int $uid)
    {
        $data = ["uid" => $uid];
        if ($error = $this->validate($data, ["uid" => "required|int"])) {
            return $error;
        }
        return $this->httpPost(Router::IS_ALL_BLACK, $data);
    }

    /**
     * 获取全站拉黑信息
     * @param int $uid
     * @return array|void
     */
    public function getBlackInfo(int $uid)
    {
        $data = ["uid" => $uid];
        if ($error = $this->validate($data, ["uid" => "required|int"])) {
            return $error;
        }
        return $this->httpPost(Router::GET_ALL_BLACK_INFO, $data);
    }

    /**
     * 全站拉黑
     * @param int $uid
     * @param int $exp
     * @param int $source
     * @return array
     */
    public function blackDo(int $uid, int $exp, int $source)
    {
        $data = [
            "uid" => $uid,
            "exp" => $exp,
            "source"=>$source
        ];
        if ($error = $this->validate($data, [
            "uid" => "required|int",
            "exp" => "required|int",
            "source" => "required|int"
        ])) {
            return $error;
        }
        return $this->httpPost(Router::BLACK_DO, $data);
    }

    /**
     * 撤销全站拉黑
     * @param int $uid
     * @return array
     */
    public function blackUndo(int $uid)
    {
        $data = [
            "uid" => $uid,
        ];
        if ($error = $this->validate($data, [
            "uid" => "required|int",
        ])) {
            return $error;
        }
        return $this->httpPost(Router::BLACK_UNDO, $data);
    }
}