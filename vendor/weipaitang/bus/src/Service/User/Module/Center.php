<?php


namespace WptBus\Service\User\Module;


use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

/**
 * Class Center
 * @package WptBus\Service\User\Module
 * @deprecated 待废弃
 */
class Center extends BaseService
{
    //获取原始信息
    public function GetSourceInfo(int $uid, int $type)
    {
        $this->setTimeout(1500)->setRetryTimes(1);
        $ret = $this->httpPost(Router::CENTER_GET_SOURCE_INFO, [
            "uid"=>$uid,
            "type"=>$type
        ]);
        return $ret;
    }

    /**
     * uri => uid
     * @param array $uris 最多不超过200个
     * @return array
     */
    public function getUidByUris(array $uris)
    {
        $this->setTimeout(1500)->setRetryTimes(1);
        $ret = $this->httpPost(Router::CENTER_GET_UID_BY_URIS, [
            "uris" => $uris,
        ]);
        return $ret;
    }

    /**
     * uid => uri
     * @param array $uids 最多不超过200个
     * @return array
     */
    public function getUriByUids(array $uids)
    {
        $this->setTimeout(1500)->setRetryTimes(1);
        $ret = $this->httpPost(Router::CENTER_GET_URI_BY_UIDS, [
            "uids" => $uids,
        ]);
        return $ret;
    }
}