<?php


namespace App\Http\Middleware;

use App\Foundation\Constant\ProfileConst;
use App\Library\Context;
use Illuminate\Http\Request;
use \Closure;

class ProfileMiddleware
{

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
        switch ($request->getHost()) {
            case ProfileConst::HOST_PROD :
                $isChroot = false;
                $profile = ProfileConst::PROFILE_PROD;
                break;
            case ProfileConst::HOST_TEST :
                $isChroot = true;
                $profile = ProfileConst::PROFILE_TEST;
                break;
            case ProfileConst::HOST_GRAY :
                $isChroot = true;
                $profile = ProfileConst::PROFILE_GRAY;
                break;
            default:
                $isChroot = true;
                $profile = ProfileConst::PROFILE_UNKNOWN;
        }
        Context::setAttachment("isChroot", $isChroot);
        Context::setAttachment("profile", $profile);
        return $next($request);
    }

}