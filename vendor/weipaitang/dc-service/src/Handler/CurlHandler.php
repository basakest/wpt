<?php

namespace WptDataCenter\Handler;

use WptDataCenter\Exception\DataCenterException;
use WptDataCenter\Logger\Log;
use Throwable;

/**
 * Class CurlHandler
 * @package WptDataCenter\Handler
 */
class CurlHandler
{
    /**
     * @var $curl
     */
    public $curl;

    /**
     * @var int $timeout
     */
    protected $timeout = 2;

    /**
     * @var int $connectTimeout
     */
    protected $connectTimeout = 2;

    /**
     * @var int 重试次数
     */
    protected $retries = 1;

    /**
     * @var $instance
     */
    protected static $instance;

    /**
     * @var string $method
     */
    protected $method = 'POST';

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var int $traceOpts
     */
    protected $traceOpts = DEBUG_BACKTRACE_IGNORE_ARGS;

    /**
     * @var int $traceLimit
     */
    protected $traceLimit = 5;

    /**
     * @return CurlHandler
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();

        }
        return self::$instance;
    }


    /**
     * @param $uri
     * @param $params
     * @return array
     * @throws DataCenterException | Throwable
     */
    public function go($uri, $params): array
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $this->getEndpoint() . '/' . ltrim($uri, '/'));
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt_array($this->curl, $this->getDefaultOpts());
        $retries = $this->getRetries();
        $uniqueId = $this->getTraceId();

        $retval = [];
        do {
            $retries--;
            $start = microtime(true);
            $result = curl_exec($this->curl);
            $duration = round((microtime(true) - $start) * 1000, 3);
            $errorCode = curl_errno($this->curl);
            $httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
            $errorInfo = curl_error($this->curl);

            $context = [
                'unique_id' => $uniqueId,
                'path' => $uri,
                'curlErr' => $errorInfo,
                'params' => $params,
                'time' => $duration . "ms",
                'curlCode' => $errorCode,
                'httpCode' => $httpCode,
            ];

            if ($errorCode == 0 && $httpCode == 200) {
                $retval = json_decode($result, true);
                if ($retval['code'] >= 200000) {
                    $this->close();
                    $context['trace'] = $this->getTraceContext();
                    $emsg = empty($retval['msg']) ? '未知错误' : $retval['msg'];
                    $context['msg'] = $emsg;
                    $context['uniqueId'] = $uniqueId;
                    Log::error("dc-call", "数据应用服务报错:业务错误", $context);
                    throw new DataCenterException("数据应用服务报错:" . $emsg, $retval['code'] ?? 0);
                }
                Log::info("dc-call", "数据应用服务:", $context);
                break;
            }
        } while ($retries > 0);

        $this->close();
        if ($errorCode != 0 || $httpCode != 200) {
            $context['trace'] = $this->getTraceContext();
            $ecode = ($errorCode != 0) ? $errorCode : $httpCode;
            $msg = $errorInfo;
            if ($errorCode == 28) {
                $msg = '请求超时';
            }
            if ($httpCode == 500) {
                $info = json_decode($result, true);
                if (isset($info['detail']) && strlen($info['detail']) > 0) {
                    $msg = $info['detail'];
                }
            }
            $context['msg'] = $msg;
            $context['uniqueId'] = $uniqueId;
            $errTypeMsg = ($errorCode == 28) ? '请求超时' : '数据异常';
            Log::error("dc-call", "数据应用服务请求报错:" . $errTypeMsg, $context);
            throw new DataCenterException($msg, $ecode);
        }
        return $retval;
    }

    /**
     * @return array
     */
    private function getTraceContext()
    {
        $debugTrace = debug_backtrace($this->getTraceOpts(), $this->getTraceLimit() + 1);
        array_shift($debugTrace);
        return $debugTrace;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return rtrim($this->endpoint == '' ? env("MICRO_GATEWAY", 'http://localhost:8080') : $this->endpoint, '/');
    }

    /**
     * @param string $endpoint
     * @return CurlHandler
     */
    public function setEndpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * @return array
     */
    private function getDefaultOpts()
    {
        return [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => $this->getConnectTimeout(),
            CURLOPT_TIMEOUT => $this->getTimeout(),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json; charset=utf-8',
                'traceId: ' . $this->getTraceId(),
                'unique_id: ' . $this->getTraceId(),
            ],
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
        ];
    }

    /**
     * close();
     */
    protected function close()
    {
        curl_close($this->curl);
    }

    /**
     * @return string
     */
    protected function getTraceId()
    {
        return defined("TRACE_ID") ? TRACE_ID : md5("dc-go" . uniqid() . rand(100000, 999999));
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     * @return CurlHandler
     */
    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    /**
     * @param int $connectTimeout
     * @return CurlHandler
     */
    public function setConnectTimeout(int $connectTimeout)
    {
        $this->connectTimeout = $connectTimeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getRetries(): int
    {
        return $this->retries == 0 ? 1 : $this->retries;
    }

    /**
     * @param int $retries
     * @return CurlHandler
     */
    public function setRetries(int $retries)
    {
        $this->retries = $retries;
        return $this;
    }

    /**
     * @return int
     */
    public function getTraceOpts(): int
    {
        return $this->traceOpts;
    }

    /**
     * @param int $traceOpts
     * @return CurlHandler
     */
    public function setTraceOpts(int $traceOpts)
    {
        $this->traceOpts = $traceOpts;
        return $this;
    }

    /**
     * @return int
     */
    public function getTraceLimit(): int
    {
        return $this->traceLimit;
    }

    /**
     * @param int $traceLimit
     * @return CurlHandler
     */
    public function setTraceLimit(int $traceLimit)
    {
        $this->traceLimit = $traceLimit;
        return $this;
    }

}
