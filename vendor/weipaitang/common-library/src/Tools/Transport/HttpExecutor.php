<?php


namespace WptCommon\Library\Tools\Transport;


class HttpExecutor extends IExecutor
{
    public function request(string $method, string $uri, array $params = [], array $opts = [], $retryTimes = 0)
    {
        do {
            try {
                $server = $this->resource->selectServer();
                $result = $this->curl($method, $server, $uri, $params, $opts, $retryTimes);
                return $result;
            } catch (\RuntimeException $e) {
                if ($retryTimes-- > 0) {
                    continue;
                }
                throw $e;
            }
        } while ($retryTimes >= 0);
    }

    private function curl($method, $server, $uri, $params, $opts, $retryTimes)
    {
        $ch = curl_init();
        $formatOpts = $this->getFormatOpts($method, $server, $uri, $params, $opts);
        curl_setopt_array($ch, $formatOpts);

        $startTime = microtime(true);
        $result = curl_exec($ch);
        $duration = round((microtime(true) - $startTime) * 1000, 3);

        $curlErrno = curl_errno($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($curlErrno != 0 || $httpCode != 200) {
            $curlError = curl_error($ch);
            $extra = ['curlErrno' => $curlErrno, 'curlError' => $curlError, 'httpCode' => $httpCode, 'currentRetry' => $retryTimes, "curlInfo" => curl_getinfo($ch)];
            $extra['startTime'] = round($startTime * 1000, 3);
            $extra['shortError'] = $this->getShortError($curlErrno, $httpCode);
            $errorLogInfo = $this->getLogInfo($formatOpts[CURLOPT_URL], $params, $opts, $result, $duration, $extra);
            $level = $retryTimes > 0 ? 'warning' : 'error';
            $this->httpLog($errorLogInfo, $level);
            throw new \RuntimeException(json_encode($errorLogInfo));
        }
        curl_close($ch);

        if ($this->resource->debug) {
            $logResult = $this->resource->debug ? $result : $this->getShortResult($result);
            $this->httpLog($this->getLogInfo($formatOpts[CURLOPT_URL], $params, $opts, $logResult, $duration));
        }
        return $result;
    }

    private function getFormatOpts(string $method, string $server, string $uri, array $params, array $customOpts): array
    {
        $opts = $this->getDefaultOpts();
        $opts[CURLOPT_URL] = $server . $uri;
        $opts[CURLOPT_CUSTOMREQUEST] = $method;
        if (!empty($params)) {
            if ($opts[CURLOPT_CUSTOMREQUEST] == 'POST') {
                $opts[CURLOPT_POSTFIELDS] = json_encode($params, JSON_UNESCAPED_UNICODE);
            } else {
                $opts[CURLOPT_URL] .= (strpos($opts[CURLOPT_URL], '?') === false ? '?' : '&') . http_build_query($params);
            }
        }
        if (isset($customOpts['headers'])) {
            foreach ($customOpts['headers'] as $key => $value) {
                $opts[CURLOPT_HTTPHEADER][] = sprintf("%s: %s", $key, $value);
            }
        }
        // 设置超时时间超过2秒，同时设置网关的超时时间
        if ($opts[CURLOPT_TIMEOUT_MS] > 2000) {
            $opts[CURLOPT_HTTPHEADER][] = sprintf("%s: %s", "Grpc-Timeout", $opts[CURLOPT_TIMEOUT_MS] . "m");
        }

        // 设置token
        if ($this->resource->token) {
            $opts[CURLOPT_HTTPHEADER][] = sprintf("%s: %s", "client-token", $this->resource->token);
        }
        // referer
        if (PHP_SAPI === 'cli') {
            $argv = $_SERVER['argv'] ?? [];
            $argv = array_slice($argv, 0, 2);
            $referer = implode(" ", $argv);
        } else {
            $parseUrl = parse_url($_SERVER["REQUEST_URI"] ?? "");
            $referer = $parseUrl["path"] ?? "";
        }
        $opts[CURLOPT_HTTPHEADER][] = sprintf("%s: %s", "referer", $referer);
        return $opts;
    }

    private function getDefaultOpts(): array
    {
        return [
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_NOSIGNAL => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT_MS => $this->resource->connectTimeout,
            CURLOPT_TIMEOUT_MS => $this->resource->readTimeout,
            CURLOPT_HTTPHEADER => $this->getDefaultHeader(),
        ];
    }

    private function getDefaultHeader(): array
    {
        return [
            'Content-Type: application/json; charset=utf-8',
        ];
    }

    private function httpLog($info, $level = 'info')
    {
        if ($level == 'error') {
            $this->logger->error("transport-http-{$this->resource->name}", "{$this->resource->name}服务http请求" . $info["shortError"] ?? "错误" , $info);
        } else if ($level == 'warning') {
            $this->logger->warning("transport-http-{$this->resource->name}", "{$this->resource->name}服务http请求"  . $info["shortError"] ?? "错误", $info);
        } else {
            $this->logger->info("transport-http-{$this->resource->name}", "{$this->resource->name}服务http请求成功", $info);
        }
    }

    private function getShortError($curlErrno, $httpCode)
    {
        if ($curlErrno == 28) {
            return "网关超时";
        }
        if ($httpCode == 408) {
            return "网关请求服务超时";
        }
        if ($curlErrno != 0) {
            return "curl错误:$curlErrno";
        }
        if ($httpCode != 200) {
            return "http错误:$httpCode";
        }
        return "错误";
    }

    private function getShortResult($result)
    {
        $resultArr = json_decode($result, true);
        if (isset($resultArr['code']) && isset($resultArr['msg'])) {
            return ['code' => $resultArr['code'], 'msg' => $resultArr['msg']];
        }
        return [];
    }

    private function getLogInfo($url, $body, $opts, $result, $duration, $extra = [])
    {
        $info = [
            'name' => $this->resource->name,
            'url' => $url,
            'body' => $body,
            'opts' => $opts,
            'result' => $result,
            'duration' => $duration,
        ];

        if ($extra) {
            $info = array_merge($info, $extra);
        }

        if (isset($opts['headers']['unique_id'])) {
            $info['uniqueId'] = $opts['headers']['unique_id'];
        }
        if (isset($opts['headers']['traceId'])) {
            $info['traceId'] = $opts['headers']['traceId'];
        }
        if (isset($opts['headers']['bizId'])) {
            $info['bizId'] = $opts['headers']['bizId'];
        }

        return $info;
    }
}