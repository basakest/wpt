<?php


namespace WptBus\Service\User\Module;


use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class UserType extends BaseService
{
    // 获取绑定信息
    public function getBindInfo(string $condition)
    {
        $params = ["condition" => $condition];
        if ($error = $this->validate($params, ['condition' => 'required'])) {
            return $error;
        }
        $this->setTimeout(1500)->setRetryTimes(1);
        return $this->httpPost(Router::PLATFORM_GET_BIND_USER_INFO, $params);
    }

    // 获取所有信息
    public function getAllInfo(string $condition)
    {
        $params = ["condition" => $condition];
        if ($error = $this->validate($params, ['condition' => 'required'])) {
            return $error;
        }
        $this->setTimeout(1500)->setRetryTimes(1);
        return $this->httpPost(Router::PLATFORM_GET_ALL_USER_INFO, $params);
    }

    // 获取是否订阅
    public function getIsSubscribe(int $uid)
    {
        $params = ["uid" => $uid];
        if ($error = $this->validate($params, ['uid' => 'required'])) {
            return $error;
        }
        $this->setTimeout(1500)->setRetryTimes(1);
        return $this->httpPost(Router::PLATFORM_GET_IS_SUBSCRIBE, $params);
    }

}