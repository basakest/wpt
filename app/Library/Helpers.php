<?php

use App\Library\Context;
use App\Library\Ding;
use WptCommon\Library\Facades\MLogger;
use Rainbow\Rainbow;

if (!function_exists('rainbow_get')) {
    /**
     * 获取配置中心配置
     * @param string $project
     * @param string $key
     * @return array|mixed
     */
    function rainbow_get(string $project, string $key)
    {
        $isChroot = Context::getAttachment("isChroot", false);
        $rainbow = Rainbow::getInstance($isChroot);
        try {
            $result = $rainbow->getConfig($project, $key);
            if (empty($result->success)) {
                MLogger::error('rainbow_get', '无配置信息，赶紧前往配置中心修复！', []);
            }
            return json_decode($result->data, true);
        } catch (Exception $e) {
            Ding::wechatGroup('rainbow_get', $e->getMessage());
            return [];
        }
    }
}