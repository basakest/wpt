<?php


namespace App\Contracts\User;

/**
 * 用户服务接口
 * Interface IUserService
 * @package App\Contracts\User
 */
interface IUserService
{

    /**
     * 获取用户登陆信息
     * @param string $token
     * @return array
     */
    public function getUserInfo(string $token):array;


    /**
     * 校验用户权限
     *
     * @param string $unionId 用户 UnionID
     * @param int $permissionId 权限 ID
     * @param string $url 访问链接
     * @return bool
     */
    public function validatePermission(string $unionId, int $permissionId, string $url):bool;


    /**
     * 获取用户权限列表
     *
     * @param string $unionId 用户 sso 的 unionid
     * @return array
     */
    public function getPowers(string $unionId):array;



    /**
     * 上报操作日志
     * @param string $unionId
     * @param int $permissionId
     * @param string $url
     * @param array $requestData
     * @param string $responseData
     * @param array $customData
     * @return bool
     */
    public function reportLog(
        string $unionId, int $permissionId, string $url,
        array $requestData, string $responseData, array $customData
    ):bool;
}