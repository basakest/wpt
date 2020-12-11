<?php


namespace App\Service\User\Callers;

use App\Exceptions\ApiException;
use App\Exceptions\UnauthorizedException;
use App\Library\HttpCaller;
use Throwable;

class AuthCaller extends HttpCaller
{
    /**
     * 响应码
     */
    const RESULT_OK = 0;


    public function __construct()
    {
        $authCnf = config('user.auth');

        $url = $authCnf['host'];
        $timeout = $authCnf['timeout'];

        parent::__construct($url, $timeout);
    }

    /**
     * @param $body
     * @return array
     * @throws ApiException
     */
    public function validatePermission($body)
    {
        $uri = "/v2/common/validateuserpermission";
        return $this->performRequest('GET', $uri, [
            "query" => $body
        ]);
    }

    /**
     * @param $body
     * @return array
     * @throws ApiException
     */
    public function getPowers($body)
    {
        $uri = "/v2/common/mypower";
        return $this->performRequest('GET', $uri, [
            "query" => $body
        ]);
    }


    /**
     * 上报操作日志
     * @param $body
     * @return array
     * @throws ApiException
     */
    public function reportLog($body)
    {
        $uri = "/v2/comlog/basic";
        return $this->performRequest('POST', $uri, [
            "form_params" => $body
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
            throw new UnauthorizedException($content['msg']);
        }

        return $content['data'] ?? [];
    }

}