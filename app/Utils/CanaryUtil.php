<?php


namespace App\Utils;

use App\Facades\ConfigCenter\NifflerConfig;

// 放量工具
class CanaryUtil
{
    /**
     * @param string $group
     * @param string $tag
     * @param int $userinfoId
     * @return bool
     *  放量对比工具
     */
    public static function config(string $group, string $tag, int $userinfoId)
    {
        // 从配置中心获取配置
        $ret = NifflerConfig::getConfig('api.weipaitang.com', $group);
        $config = [];
        if ($ret->success && !empty($ret->data)) {
            $data = json_decode($ret->data, true);
            if (!empty($data)) {
                $config = $data;
            }
        }
        if (empty($config) || !isset($config[$tag])) {
            return false;
        }
        $percentFlag = false;
        $whiteListFlag = false;
        if (isset($config[$tag]['percent'])) {
            $method = 'random';
            if (isset($config[$tag]['method'])) {
                $method = $config[$tag]['method'];
            }
            switch ($method) {
                case 'random':
                    $percentFlag = rand(1, 100) <= intval($config[$tag]['percent']);
                    break;
                case 'user':
                    $percentFlag = (100 - $userinfoId % 100) <= intval($config[$tag]['percent']);
                    break;
                default:
                    return false;
            }
        }
        if (isset($config[$tag]['whiteUsers']) && !empty($config[$tag]['whiteUsers'])) {
            $whiteListFlag = in_array($userinfoId, $config[$tag]['whiteUsers']);
        }
        return $percentFlag || $whiteListFlag;
    }
}
