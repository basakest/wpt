<?php


namespace App\Http\Middleware;

use App\Contracts\User\IUserService;
use App\Library\Context;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use WptCommon\Library\Facades\MLogger;

class OperationLogMiddleware
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
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Perform any final actions for the request lifecycle.
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function terminate(Request $request, Response $response)
    {
        $permissionId = (int)$request->input('permissionId');
        $userInfo = $request->attributes->get("userInfo");
        if (empty($permissionId) || empty($userInfo["unionid"])) {
            return;
        }
        try {
            $this->userService->reportLog(
                $userInfo["unionid"], $permissionId, $request->path(),
                $request->all(), $response->getContent(), Context::getAttachments()
            );
        } catch (\Throwable $exception) {
            MLogger::warning("operationLog", '操作日志记录失败: '.$exception->getMessage(), [
                'url' => $request->fullUrl(),
                'operation' => Context::getAttachments()
            ]);
        }

    }

}