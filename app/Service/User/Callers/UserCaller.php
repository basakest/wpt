<?php


namespace App\Service\User\Callers;

use App\Exceptions\ApiException;
use App\Exceptions\UnLoginException;
use App\Library\HttpCaller;
use Throwable;

class UserCaller extends HttpCaller
{
    /**
     * 响应码
     */
    const RESULT_OK = 0;


    public function __construct()
    {
        $userCnf = config('user.login');

        $url = $userCnf['host'];
        $timeout = $userCnf['timeout'];
        parent::__construct($url, $timeout);
    }

    /**
     * @param string $token
     * @return array
     * @throws ApiException
     */
    public function getUser(string $token)
    {
        $uri = "/api/verifyToken";
        return $this->performRequest('GET', $uri, [
            "query" => [
                'token' => $token,
                'domain' => config('user.login.domain')
            ]
        ]);
    }


    /**
     * 发送请求
     *
     * @param string $method
     * @param string $uri
     * @param array $options
     *
     * @return array
     * @throws ApiException
     */
    protected function performRequest($method, $uri = '', array $options = [])
    {
        try {
            $options['http_errors'] = false;

            $response = $this->client->request($method, $uri, $options);
        } catch (Throwable $e) {
            throw new ApiException('接口请求失败1');
        }

        $status = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $content = json_decode($body, true);

        if ($status != 200 || !isset($content["code"])) {
            throw new ApiException('接口请求失败2');
        }

        if ($content['code'] !== static::RESULT_OK) {
            throw new UnLoginException($content['msg']);
        }

        return $content['data'] ?? [];
    }
}