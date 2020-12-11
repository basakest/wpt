<?php


namespace WptBus\Service;

use WptBus\Lib\Error;
use WptBus\Lib\Log;
use WptBus\Lib\Response;
use WptBus\Lib\Utils;
use WptBus\Lib\Validator;
use WptCommon\Library\Tools\Transport\Executor;

class BaseService
{
    use CustomHttpConfig;
    protected $serviceName;
    protected $config = [];

    public function init($serviceName, $config)
    {
        $this->serviceName = $serviceName;
        $this->config = $config;
    }

    protected function validate($params, $rules)
    {
        if (Validator::check($params, $rules)) {
            return;
        }
        return Response::byBus(Error::INVALID_ARGUMENT);
    }

    protected function getSign(array $httpConfig, array &$params)
    {
        if (isset($httpConfig["checkSign"]) && $httpConfig["checkSign"] == true) {
            unset($params["sign"]);
            $params["timestamp"] = (string)time();
            $params["sign"] = Utils::getSign($params, $httpConfig["signKey"]);
        }
    }

    protected function httpPost(string $uri, array $params = [], $header = [], $retryTimes = 0)
    {
        $httpConfig = $this->config['http'];
        if ($this->customHttpConfig) {
            $httpConfig = array_merge($httpConfig, $this->customHttpConfig);
        }
        if (empty($httpConfig) || empty($httpConfig['servers'])) {
            return Response::byBus(Error::INVALID_CONFIG);
        }
        $defaultHeader = Utils::getDefaultHeader($this->serviceName, $uri);
        $opts = ['headers' => array_merge($defaultHeader, $header, $this->customHeader)];
        if ($this->customRetryTimes >= 0) {
            $retryTimes = $this->customRetryTimes;
        }
        $this->resetHttpConfig();
        try {
            $result = Executor::loadHttpExecutor($httpConfig)->request("POST", $uri, $params, $opts, $retryTimes);
        } catch (\RuntimeException $e) {
            return Response::byBus(Error::TRANSPORT, Error::getBusErrorMsgInfo(Error::TRANSPORT, $e->getMessage()));
        }
        $data = json_decode($result, true);
        if (!isset($data['code'])) {
            return Response::byBus(Error::RETURN_FORMAT_ERROR, Error::getBusErrorMsgInfo(Error::RETURN_FORMAT_ERROR, json_encode($data)));
        }
        if (!$this->isSpecialUri($uri)) {
            if ($data['code'] == -1 || $data['code'] == 202101 || ($data['code'] > 0 && $data['code'] < 200000)) {
                $errorLogInfo = ['uri' => $uri, 'params' => $params, 'data' => $data, 'unique_id' => $opts['headers']['unique_id']];
                Log::error($this->serviceName, "{$this->serviceName}服务异常", $errorLogInfo);
                return Response::byBus(Error::SYSTEM_EXCEPTION, Error::getBusErrorMsgInfo(Error::SYSTEM_EXCEPTION, json_encode($errorLogInfo)));
            }
        }
        return Response::bySrv($data);
    }

    protected function isSpecialUri($uri)
    {
        if (in_array($uri, ['user/punish/filter'])) {
            return true;
        }
        return false;
    }

    protected function dealResultData(&$ret, callable $func)
    {
        if ($ret['code'] != 0) {
            return;
        }
        $ret['data'] = $func($ret['data']);
    }
}

/**
 * 自定义http配置信息，若SDK使用和业务方都使用，已业务方为准，这里逻辑是已第一次设置的为准
 * Class CustomHttpConfig
 * @package WptBus\Service
 */
trait CustomHttpConfig
{

    protected $customHttpConfig = [];
    protected $customHeader = [];
    protected $customRetryTimes = -1;

    /**
     * @param array $config
     * @param array $header
     * @param int $retryTimes
     * @return $this
     */
    public function setHttpConfig(array $config, array $header = [], int $retryTimes = -1)
    {
        if ($config) {
            $this->customHttpConfig = array_merge($config, $this->customHttpConfig);
        }
        if ($header) {
            $this->customHeader = array_merge($header, $this->customHeader);
        }
        if ($retryTimes >= 0 && $this->customRetryTimes == -1) {
            $this->customRetryTimes = $retryTimes;
        }
        return $this;
    }

    /**
     * @param int $readTimeout
     * @param int $connectTimeout
     * @return $this
     */
    public function setTimeout(int $readTimeout, int $connectTimeout = 0)
    {
        $config = [];
        if ($readTimeout > 0) {
            $config['readTimeout'] = $readTimeout;
        }
        if ($connectTimeout > 0) {
            $config['connectTimeout'] = $connectTimeout;
        }
        if ($config) {
            $this->setHttpConfig($config);
        }
        return $this;
    }

    /**
     * @param string $uniqueId
     * @return $this
     */
    public function setUniqueId(string $uniqueId)
    {
        if (!empty($uniqueId)) {
            $header = ["unique_id" => $uniqueId];
            $this->setHttpConfig([], $header);
        }
        return $this;
    }

    /**
     * @param int $retryTimes
     * @return $this
     */
    public function setRetryTimes(int $retryTimes)
    {
        if ($retryTimes >= 0) {
            $this->setHttpConfig([], [], $retryTimes);
        }
        return $this;
    }

    private function resetHttpConfig()
    {
        $this->customHttpConfig = [];
        $this->customHeader = [];
        $this->customRetryTimes = -1;
    }
}
