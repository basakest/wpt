<?php


namespace App\Service\User;

use App\Contracts\User\IUserService;
use App\Exceptions\ApiException;
use App\Exceptions\UnauthorizedException;
use App\Library\Context;
use App\Service\User\Callers\AuthCaller;
use App\Service\User\Callers\UserCaller;

class UserManager implements IUserService
{

    /**
     * 获取用户登陆信息
     * @param string $token
     * @return array
     * @throws ApiException
     */
    public function getUserInfo(string $token):array
    {
        $caller = new UserCaller();
        return $caller->getUser($token);
    }

    /**
     * 校验用户权限
     *
     * @param string $unionId 用户 UnionID
     * @param int $permissionId 权限 ID
     * @param string $url 访问链接
     * @return bool
     * @throws ApiException
     */
    public function validatePermission(string $unionId, int $permissionId, string $url):bool
    {
        if (empty($unionId) || $permissionId <= 0) {
            return false;
        }

        $caller = new AuthCaller();
        $result = $caller->validatePermission([
            'platform' => config('user.auth.platform'),
            'dingId' => $unionId,
            'permissionId' => $permissionId,
            'url' => $url
        ]);

        return !empty($result);
    }

    /**
     * 获取用户权限列表
     *
     * @param string $unionId 用户 sso 的 unionid
     * @return array
     * @throws ApiException
     */
    public function getPowers(string $unionId):array
    {
        if (empty($unionId)) {
            throw new UnauthorizedException("获取权限列表失败");
        }

        $caller = new AuthCaller();
        return $caller->getPowers([
            'platform' => config('user.auth.platform'),
            'dingId' => $unionId
        ]);
    }



    /**
     * 上报操作日志
     * @param string $unionId
     * @param int $permissionId
     * @param string $url
     * @param array $requestData
     * @param string $responseData
     * @param array $customData
     * @return bool
     * @throws ApiException
     */
    public function reportLog(
        string $unionId, int $permissionId, string $url,
        array $requestData, string $responseData, array $customData
    ):bool
    {
        if ($permissionId <= 0) {
            return false;
        }

        $data = [
            'platform' => config('user.auth.platform'),
            'dingId'    => $unionId,
            'permissionId' => $permissionId,
            'url'       => $url,
            'dingName' => Context::getAttachment("name"),
            'dingMobile' => Context::getAttachment("mobile"),
            'in' => json_encode($requestData, JSON_UNESCAPED_UNICODE),
            'out' => json_encode($responseData, JSON_UNESCAPED_UNICODE),
            'custom' => json_encode([
                "req" => $requestData,
                "rsp" => $responseData,
                "context" => $customData,
            ], JSON_UNESCAPED_UNICODE),
        ];
        $caller = new AuthCaller();
        $result = $caller->reportLog($data);

        return !empty($result);
    }


}