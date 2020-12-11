<?php

namespace App\Utils;

use Illuminate\Support\Facades\Cookie;

/**
 * EnvUtil.php
 * describe:
 * Created On 2020/5/6 下午5:43
 * Created By Jax
 */
class EnvUtil
{
    /**
     * 获取环境
     * @return string
     */
    public static function getApiEnvValueWithTest()
    {
        $env = env('ENV', '');
        if ($env === 'TEST') {
            return env('APP_ENV_MARK', '');
        }
        $envVar = Cookie::get('wpt_debug');
        $envKey = 'prd';
        if ($envVar) {
            if ($envVar == '9cb88042edc55bf85c22e89cf880c63b' || $envVar == '8b75e68a81477d54a1fd97aa0eae97b3') {
                $envKey = 'gray';
            }
        }
        return $envKey;
    }
}
