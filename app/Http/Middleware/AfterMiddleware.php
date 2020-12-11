<?php
/**
 *
 * @auther heyu 2020/7/1
 */

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use \Closure;
use \Illuminate\Http\Request;
use Illuminate\Http\Response;
use WptCommon\Library\Facades\MLogger;

class AfterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        if ($response->exception) {
            if ($response->exception instanceof ApiException) {
                MLogger::warning("api-exception", $response->exception->getMessage(), [
                    'trace' => $response->exception->getTraceAsString()
                ]);
                $response = response()->json(['code' => $response->exception->getCode() ?: 1, 'msg' => $response->exception->getMessage()]);
            } elseif (! app()->environment('local')) {
                MLogger::error("api-exception", $response->exception->getMessage(), [
                    'trace' => $response->exception->getTraceAsString()
                ]);
                $response = response()->json(['code' => 100, 'msg' => 'error']);
            }
        } else {
            $response = response()->json(['code' => 0, 'msg' => 'success', 'data' => $response->getOriginalContent() ?: (object)[]]);
        }

        return $this->responseHeader($response);
    }

    /**
     * 添加 CORS 响应头信息
     *
     * @param  Response  $response
     * @return mixed
     * @author heyu  2020/7/23 18:07
     */
    private function responseHeader($response)
    {
        $response->setCharset('utf-8');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('access-control-allow-origin', 'https://' . env('FRONTEND_DOMAIN'));

        return $response;
    }
}
