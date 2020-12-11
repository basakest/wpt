<?php


namespace WptUtils\Http;

use WptUtils\Contracts\Http\ClientInterface;
use WptUtils\Exception\HttpException;

/**
 * curl简单实现,推荐使用guzzlehttp
 *
 * Class Http
 * @package WptUtils
 */
final class Client implements ClientInterface
{
    /**
     * 默认UA
     * @var string
     */
    const USER_AGENT = 'PHP Curl/1.0.0';

    /**
     * 最大并发数
     * @var int
     */
    const MAX_CONCURRENCY = 512;

    /**
     * @var $key
     */
    protected $key;

    /**
     * @var array $keyMaps
     */
    protected $keyMaps = [];

    /**
     * @var
     */
    protected $curl;

    /**
     * @var array
     */
    protected $cookies = [];

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * 记录curl选项
     *
     * @var array
     */
    protected $opts = [];

    /**
     * 重试次数
     *
     * @var int
     */
    protected $retries = 3;

    /**
     * 超时时间 单位:毫秒
     *
     * @var int
     */
    protected $timeout = 3000;

    /**
     * 连接超时时间 单位:毫秒
     *
     * @var int
     */
    protected $connectTimeout = 5000;

    /**
     * select 超时
     * @var float
     */
    // protected $selectTimeout = 3.0;

    /**
     * 错误码
     *
     * @var int
     */
    protected $errorCode = 0;

    /**
     * 错误消息
     *
     * @var string
     */
    protected $errorMessage = '';

    /**
     * http状态吗
     *
     * @var int
     */
    protected $statusCode = 0;

    /**
     * 延迟
     *
     * @var bool
     */
    protected $delay = false;

    /**
     * 资源集合
     *
     * @var array
     */
    protected $resources = [];

    /**
     * 请求响应
     *
     * @var mixed
     */
    protected $response;

    /**
     * @var array
     */
    protected $responseHeader = [];

    /**
     * @var null
     */
    protected static $instance = null;


    /**
     * Curl constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * @return static
     */
    public static function instance(): self
    {
        return new static();
    }

    /**
     * @param $url
     * @param array $data
     * @return Client
     * @throws HttpException
     */
    public function get(string $url, $data = null)
    {
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $this->setOpt(CURLOPT_HTTPGET, true);
        $this->setOpt(CURLOPT_URL, $this->buildUrl($url, $data));
        return $this->exec();
    }

    /**
     * @param $url
     * @param $data
     * @return Client
     * @throws HttpException
     */
    public function post(string $url, $data = null)
    {
        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        $this->preparePayload($data);
        return $this->exec();
    }

    /**
     * @param string $url
     * @param null $data
     * @return $this
     * @throws HttpException
     */
    public function head(string $url, $data = null)
    {
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
        $this->setOpt(CURLOPT_NOBODY, true);
        $this->setOpt(CURLOPT_URL, $this->buildUrl($url, $data));
        return $this->exec();
    }

    /**
     * @param $url
     * @param array $data
     * @param bool $payload
     * @return $this
     * @throws HttpException
     */
    public function put(string $url, $data = null, $payload = false)
    {
        if (!empty($data)) {
            if ($payload === false) {
                $url = $this->buildUrl($url, $data);
            } else {
                $this->preparePayload($data);
            }
        }
        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        return $this->exec();
    }

    /**
     * @param $url
     * @param array $data
     * @param bool $payload
     * @return $this
     * @throws HttpException
     */
    public function patch(string $url, $data = null, $payload = false)
    {
        if (!empty($data)) {
            if ($payload === false) {
                $url = $this->buildUrl($url, $data);
            } else {
                $this->preparePayload($data);
            }
        }

        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        return $this->exec();
    }

    /**
     * @param $url
     * @param array $data
     * @param bool $payload
     * @return $this
     * @throws HttpException
     */
    public function delete(string $url, $data = null, $payload = false)
    {
        if (!empty($data)) {
            if ($payload === false) {
                $url = $this->buildUrl($url, $data);
            } else {
                $this->preparePayload($data);
            }
        }

        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->exec();
    }

    /**
     * @param $url
     * @param array $data
     * @param bool $payload
     * @return $this
     * @throws HttpException
     */
    public function options(string $url, $data = null, $payload = false)
    {
        if (!empty($data)) {
            if ($payload === false) {
                $url = $this->buildUrl($url, $data);
            } else {
                $this->preparePayload($data);
            }
        }

        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        return $this->exec();
    }

    /**
     * 构建url
     *
     * @param $url
     * @param string $data
     * @return string
     */
    protected function buildUrl($url, $data = null)
    {
        $query = '';
        if (!empty($data)) {
            $mark = strpos($url, '?') > 0 ? '&' : '?';
            if (is_string($data)) {
                $query .= $mark . $data;
            } elseif (is_array($data)) {
                $query .= $mark . http_build_query($data, '', '&');
            }
        }
        return $url . $query;
    }


    /**
     * 设置重试次数
     *
     * @param int $retries
     * @return Client
     */
    public function setRetries(int $retries)
    {
        $this->retries = $retries;
        return $this;
    }

    /**
     * 超时时间 单位:毫秒
     *
     * @param int $timeout
     * @return Client
     */
    public function setTimeout(int $timeout)
    {
        $this->setOpt(CURLOPT_TIMEOUT_MS, $timeout);
        return $this;
    }

    /**
     * 连接超时时间 单位:毫秒
     *
     * @param int $connectTimeout
     * @return Client
     */
    public function setConnectTimeout(int $connectTimeout)
    {
        $this->setOpt(CURLOPT_CONNECTTIMEOUT_MS, $connectTimeout);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param array|object|string $data
     */
    protected function preparePayload($data)
    {
        if (is_array($data) || is_object($data)) {
            $skip = false;
            foreach ($data as $key => $value) {
                if ($value instanceof \CurlFile) {
                    $skip = true;
                }
            }

            if (!$skip) {
                $data = http_build_query($data, '', '&');
            }
        }

        $this->setOpt(CURLOPT_POSTFIELDS, $data);
    }

    /**
     * 初始化
     */
    protected function init()
    {
        $this->setOpt(CURLOPT_HEADER, false);
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->setOpt(CURLOPT_NOSIGNAL, true);
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $this->setOpt(CURLOPT_USERAGENT, static::USER_AGENT);
        $this->setOpt(CURLOPT_TIMEOUT_MS, $this->timeout);
        $this->setOpt(CURLOPT_CONNECTTIMEOUT_MS, $this->connectTimeout);
        $this->setOpt(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    }

    /**
     * @return $this
     * @throws HttpException
     */
    protected function exec()
    {
        if (parse_url($this->opts[CURLOPT_URL]) === false) {
            throw new \InvalidArgumentException(
                "Unable to parse URI: " . $this->opts[CURLOPT_URL] ?? ''
            );
        }
        if ($this->delay) {
            return $this;
        }
        $this->generateTraceId();
        $this->curl = curl_init();
        curl_setopt_array($this->curl, $this->opts);

        if ($this->retries <= 0) {
            $this->retries = 1;
        }
        do {
            $this->retries--;
            $this->response = curl_exec($this->curl);
            $this->errorCode = curl_errno($this->curl);
            $this->errorMessage = curl_error($this->curl);
            $this->statusCode = intval(curl_getinfo($this->curl, CURLINFO_HTTP_CODE));

            if ($this->errorCode == 0 && $this->statusCode == 200) {
                break;
            }
        } while ($this->retries > 0);

        if (!empty($this->errorMessage) && $this->errorCode != 0) {
            throw new HttpException($this->errorMessage, $this->errorCode);
        }
        return $this;
    }

    /**
     * @param \Closure $callback
     * @return Client
     */
    public function add(\Closure $callback)
    {
        $instance = new static();
        $instance->delay = true;
        $instance->generateTraceId();
        $callback($instance);
        $this->resources[] = $instance;
        return $this;
    }

    /**
     * 开始执行批量请求
     * @return $this
     * @throws HttpException
     */
    public function start()
    {
        $this->checkResource();

        $response = [];
        $multi = curl_multi_init();
        try {
            curl_multi_setopt($multi, CURLMOPT_MAXCONNECTS, floor(static::MAX_CONCURRENCY / 2));
            foreach ($this->resources as $resource) {
                if ($resource instanceof static) {
                    $curl = curl_init();
                    $this->keyMaps[(int)$curl] = $resource->key;
                    curl_setopt_array($curl, $resource->opts);
                    curl_multi_add_handle($multi, $curl);
                }
            }

            do {
                do {
                    $execrun = curl_multi_exec($multi, $running);
                } while ($execrun == CURLM_CALL_MULTI_PERFORM);

                if ($execrun != CURLM_OK) {
                    break;
                }
                while ($done = curl_multi_info_read($multi)) {
                    if ($this->keyMaps) {
                        $key = $this->keyMaps[(int)$done['handle']] ?? '';
                        if (empty($key) && $key !== "0") {
                            continue;
                        }
                        $response[$key] = curl_multi_getcontent($done['handle']);
                    } else {
                        $response[] = curl_multi_getcontent($done['handle']);
                    }
                    curl_multi_remove_handle($multi, $done['handle']);
                }
                if ($running) {
                    if (curl_multi_select($multi) == -1) {
                        usleep(250);
                    }
                }
            } while ($running);
            curl_multi_close($multi);
        } catch (\Throwable $e) {
            if (is_resource($multi)) {
                curl_multi_close($multi);
            }
            throw new HttpException($e->getMessage(), $e->getCode());
        }
        $this->response = array_filter($response);
        return $this;
    }

    /**
     * @throws HttpException
     */
    private function checkResource()
    {
        if (count($this->resources) > static::MAX_CONCURRENCY) {
            throw new HttpException("超出最大并发数:" . static::MAX_CONCURRENCY, 71001);
        }
        if (0 == count($this->resources)) {
            throw new HttpException("无有效http请求,请添加有效请求", 71002);
        }
    }

    /**
     * 生成traceid信息
     */
    private function generateTraceId()
    {
        $this->setHeader("unique_id", $this->uniqueId());
        $this->setHeader("traceId", $this->uniqueId());
    }

    /**
     * @return string
     */
    private function uniqueId()
    {
        return defined('TRACE_ID') ? TRACE_ID : md5('utils' . uniqid() . rand(100000, 999999));
    }

    /**
     * 发送json数据
     *
     * @return $this
     */
    public function asJson()
    {
        $this->setHeader("Content-Type", "application/json; charset=UTF-8");
        return $this;
    }

    /**
     * @param $username
     * @param $password
     * @return $this
     */
    public function setBasicAuthentication($username, $password)
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
        return $this;
    }


    /**
     * 设置ua
     *
     * @param $useragent
     * @return $this
     */
    public function setUserAgent(string $useragent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $useragent);
        return $this;
    }

    /**
     * 设置referer
     *
     * @param $referer
     * @return $this
     */
    public function setReferer(string $referer)
    {
        $this->setOpt(CURLOPT_REFERER, $referer);
        return $this;
    }

    /**
     * 代理服务
     *
     * @param $host
     * @param $port
     * @return $this
     */
    public function setProxy($host, $port)
    {
        $this->setOpt(CURLOPT_PROXY, $host);
        $this->setOpt(CURLOPT_PROXYPORT, $port);
        return $this;
    }

    /**
     * 设置header
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function setHeader(string $key, string $value)
    {
        $this->headers[$key] = $key . ': ' . trim($value);
        $this->setOpt(CURLOPT_HTTPHEADER, array_values($this->headers));
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setCookie(string $key, string $value)
    {
        $this->cookies[$key] = $value;
        $this->setOpt(CURLOPT_COOKIE, http_build_query($this->cookies, '', '; '));
        return $this;
    }

    /**
     * @param $option
     * @param $value
     * @return $this
     */
    protected function setOpt($option, $value)
    {
        $this->opts[$option] = $value;
        return $this;
    }


    /**
     * @param mixed $key
     * @return Client
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param $k
     * @param $headerStr
     * @return mixed|string
     */
    private function getHeader($k, $headerStr)
    {
        $headers = [];
        $headerArr = array_filter(explode("\r\n", $headerStr));
        array_shift($headerArr);

        foreach ($headerArr as $content) {
            if (empty($content)) {
                continue;
            }
            list($key, $value) = explode(':', $content, 2);
            $key = strtolower($key);
            $value = trim($value);
            $headers[$key] = $value;
        }
        return $headers[strtolower($k)] ?? '';
    }

    /**
     * @return array
     */
    public function getVersion()
    {
        return curl_version();
    }

    /**
     * error code
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * error message
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * http status code
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * close curl
     * @return $this
     */
    public function close()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
        return $this;
    }

    public function __invoke()
    {
        return $this->response;
    }

    public function __destruct()
    {
        $this->close();
    }
}
