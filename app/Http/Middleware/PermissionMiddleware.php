<?php

namespace App\Http\Middleware;

use App\Contracts\User\IUserService;
use App\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{

    /**
     * 用户授权服务
     *
     * @var IUserService
     */
    private $userService;

    /**
     * PermissionMiddleware constructor.
     * @param IUserService $userService
     */
    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * 执行权限检查
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws UnauthorizedException
     */
    public function handle(Request $request, Closure $next)
    {
        if (in_array(env('APP_ENV'), ["local"])) {
            return $next($request);
        }
        $userInfo = $request->attributes->get("userInfo");

        $permissionId = (int)$request->input('permissionId', 0);
        if (!$this->userService->validatePermission($userInfo["unionid"], $permissionId, $request->path())) {
            throw new UnauthorizedException();
        }
        return $next($request);
    }
}