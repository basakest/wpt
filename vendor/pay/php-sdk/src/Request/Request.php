<?php

namespace PayCenter\Request;

use PayCenter\Config;
use PayCenter\Exception\RequestException;
use PayCenter\Response\Response;
use JsonSerializable;
use PayCenter\Signature;

abstract class Request implements JsonSerializable
{
    const PATH = '';
    const METHOD = 'POST';
    const USERAGENT = 'pay-php-sdk/' . Config::VERSION;

    protected $curl;
    protected $host;
    protected $retry = 5;
    protected $errno = 0;
    protected $error = '';
    protected $timeout = 5000;
    protected $requestTime = 0; //接口发起调用时间
    protected $parameters = [];

    /**
     * Request constructor.
     * @throws \PayCenter\Exception\ConfigException
     */
    public function __construct()
    {
        $this->curl = curl_init();
        $this->host = Config::getHost();
        $this->product = Config::getProduct();

        curl_setopt_array($this->curl, [
            CURLOPT_HEADER => false,
            CURLOPT_NOSIGNAL => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT_MS => 300,
            CURLOPT_USERAGENT => static::USERAGENT,
            CURLOPT_CUSTOMREQUEST => static::METHOD,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf-8']
        ]);

        if (defined('CURLOPT_TCP_NODELAY')) {
            curl_setopt($this->curl, CURLOPT_TCP_NODELAY, true);
        }

        if (defined('CURLOPT_DNS_USE_GLOBAL_CACHE')) {
            curl_setopt($this->curl, CURLOPT_DNS_USE_GLOBAL_CACHE, true);
        }

        if (defined('CURLOPT_DNS_CACHE_TIMEOUT')) {
            curl_setopt($this->curl, CURLOPT_DNS_CACHE_TIMEOUT, 1800);
        }
    }

    /**
     * 发起接口请求
     * @return Response
     * @throws \PayCenter\Exception\Exception
     */
    public function request()
    {
        $this->parameters[Signature::SIGNATURE_KEY] = Signature::generate($this->parameters, Config::getKey());

        curl_setopt($this->curl, CURLOPT_TIMEOUT_MS, $this->timeout);
        curl_setopt($this->curl, CURLOPT_URL, $url = $this->host . static::PATH);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($this->parameters, JSON_UNESCAPED_UNICODE));

        $this->requestTime = time();
        $original = curl_exec($this->curl);

        $this->errno = curl_errno($this->curl);
        if ($this->errno) {
            $this->error = curl_error($this->curl);
            if ($this->shouldRetry()) {
                return self::request();
            }
            throw new RequestException("支付接口请求失败({$this->errno})：{$this->error}", $url, $this->parameters);
        }

        return new Response($original, $this);
    }

    /**
     * 是否需要重试 （连接超时 or 解析超时）
     * @return bool
     */
    private function shouldRetry()
    {
        return --$this->retry > 0 && (
            in_array($this->errno, [
                CURLE_COULDNT_CONNECT,
                CURLE_COULDNT_RESOLVE_HOST
            ], true) ||
            strpos($this->error, 'Connection timed out') !== false ||
            strpos($this->error, 'Connection time-out') !== false ||
            strpos($this->error, 'Resolving timed out') !== false
        );
    }

    /**
     * 设置请求超时时间[秒]
     * @param float $timeout
     * @return static
     */
    public function setTimeout(float $timeout)
    {
        $this->timeout = $timeout * 1000;
        return $this;
    }

    public function jsonSerialize()
    {
        return $this->parameters;
    }

    /**
     * 发起接口请求
     * @return Response
     * @throws \PayCenter\Exception\Exception
     */
    public function __invoke()
    {
        return static::request();
    }

    public function __isset($key)
    {
        return isset($this->parameters[$key]);
    }

    public function __get($key)
    {
        return $this->parameters[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    public function __unset($key)
    {
        unset($this->parameters[$key]);
    }

    public function __toString()
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }
}
