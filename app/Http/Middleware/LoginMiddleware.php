<?php

namespace App\Http\Middleware;

use App\Contracts\User\IUserService;
use App\Library\Context;
use Illuminate\Http\Request;
use \Closure;

class LoginMiddleware
{
    /**
     * 用户服务接口
     *
     * @var IUserService
     */
    private $userService;

    /**
     * LoginMiddleware constructor.
     * @param IUserService $userService
     */
    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * 获取用户登录信息
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 获取用户信息
        $uid = Context::getAttachment("uid");
        if (!empty($uid)) {
            return $next($request);
        }

        $token = $request->cookie('sso_token');
        $user = $this->userService->getUserInfo((string)$token);

        Context::setAttachment("name", $user["name"] ?? "");
        Context::setAttachment("uid", $user["id"] ?? 0);
        Context::setAttachment("mobile", $user["mobile"] ?? "");
        $request->attributes->set("userInfo", $user);

        return $next($request);
    }
}
