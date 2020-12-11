<?php


namespace App\Library;

use App\Exceptions\ApiException;
use GuzzleHttp\Client;
use Throwable;

class HttpCaller
{


    /**
     * GuzzleHttp 客户端
     *
     * @var Client
     */
    protected $client;

    /**
     * 创建 Http 调用器
     *
     * @param string $baseUri
     * @param float $timeout
     */
    public function __construct($baseUri = '', $timeout = 3.0)
    {
        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => $timeout,
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
            throw new ApiException('接口请求失败3');
        }

        return $content['data'] ?? [];
    }
}