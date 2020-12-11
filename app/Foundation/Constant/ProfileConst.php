<?php


namespace App\Foundation\Constant;

class ProfileConst
{
    /*
    |--------------------------------------------------------------------------
    | 环境域名
    |--------------------------------------------------------------------------
    */
    const HOST_PROD  = "skeleton-api.weipaitang.com"; // 生产环境
    const HOST_GRAY  = "skeleton-apigray.weipaitang.com"; // 灰度环境
    const HOST_TEST  = "skeleton-apit.weipaitang.com"; // 测试环境

    /*
    |--------------------------------------------------------------------------
    | 环境标识
    |--------------------------------------------------------------------------
    */
    const PROFILE_PROD  = "prod"; // 生产环境
    const PROFILE_GRAY  = "gray"; // 灰度环境
    const PROFILE_TEST  = "test"; // 测试环境
    const PROFILE_UNKNOWN  = "unknown"; // 未知环境

}