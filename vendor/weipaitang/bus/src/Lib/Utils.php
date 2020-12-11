<?php

namespace WptBus\Lib;

use WptBus\Exception\BusException;
use WptBus\Service\User\Router;

class Utils
{
    public static function getTraceId(): string
    {
        return defined('TRACE_ID') ? TRACE_ID : "";
    }

    public static function getBizId(): string
    {
        return defined('BIZ_ID') ? BIZ_ID : "";
    }

    public static function getUniqueid($serviceName = "")
    {
        return md5($serviceName . uniqid() . rand(100000, 999999));
    }

    public static function getDefaultHeader($serviceName = "", $uri = "")
    {
        $traceInfo = array_filter([
            'traceId' => self::getTraceId(),
            'unique_id' => defined("TRACE_ID") ? TRACE_ID : self::getUniqueid($serviceName),
            'bizId' => self::getBizId()
        ]);
        $clientInfo = self::getClientInfo();
        if (!in_array($uri, [Router::AUTH_LOGIN, Router::BIND_TELEPHONE_BIND_CHANGE, Router::BIND_TELEPHONE_AUTOMATIC_BIND, Router::MERGE_TELEPHONE, Router::CODE_SEND])) {
            unset($clientInfo["client-cookie"]);
        }
        return array_merge($traceInfo, $clientInfo);
    }

    public static function throwException(int $code, string $message)
    {
        throw new BusException($message, $code);
    }

    public static function getClientInfo(string $platform = "")
    {
        $xForwardedFor = (string)($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '');
        $ip = explode(',', $xForwardedFor)[0];
        return [
            "client-cookie" => $_SERVER['HTTP_COOKIE'] ?? '',
            "client-user-agent" => $_SERVER['HTTP_USER_AGENT'] ?? '',
            "client-remote-addr" => $ip,
            "client-x-forwarded-for" => $xForwardedFor,
            "client-referer" => $_SERVER['HTTP_REFERER'] ?? '',
            "client-platform" => $platform,
            "client-request-uri" => $_SERVER['REQUEST_URI'] ?? ''
        ];
    }

    public static function getSign(array $data, string $key)
    {
        ksort($data);
        $source = "";
        foreach ($data as $k => $val) {
            $source .= $k;
            $source .= $val;
        }
        return strtoupper(hash_hmac('md5', $source, $key));
    }

    public static function isEnvTest()
    {
        return in_array(self::getEnv(), ['LOCAL', 'TEST']);
    }

    public static function getEnv()
    {
        if (function_exists("env")) {
            return env('ENV', 'PROD');
        } else {
            return 'PROD';
        }
    }

    /**
     * 一二维数组过滤
     * @param array $result
     * @param array $filter ['xx'=>0,'cc'=>2]
     * @return array|mixed|null
     */
    public static function filterResult($result, $filter = [])
    {
        if (empty($result) || empty($filter)) {
            return $result;
        }

        if (!($isArr = is_array($result))) {
            $result = [$result];
        }
        foreach ($result as $key => $item) {
            foreach ($filter as $field => $value) {
                if (!property_exists($item, $field) or $item->$field != $value) {
                    unset($result[$key]);
                    break;
                }
            }
        }

        if (empty($result)) {
            return $isArr ? [] : null;
        }

        return $isArr ? array_values($result) : $result[0];
    }

    public static function get_property($obj, $property, $default = null)
    {
        if (!$obj) {
            return $default;
        }
        is_string($obj) and $obj = json_decode($obj, true);
        if (is_object($obj)) {
            return property_exists($obj, $property) || isset($obj->$property) ? $obj->$property : $default;
        }

        return isset($obj[$property]) ? $obj[$property] : $default;
    }

    public static function property($obj, $property, $default = null)
    {
        if (!$obj) return $default;
        is_string($obj) and $obj = json_decode($obj, true);
        if (is_object($obj)) {

            return property_exists($obj, $property) && !is_null($obj->$property) ? $obj->$property : $default;
        }

        return isset($obj[$property]) && !is_null($obj[$property]) ? $obj[$property] : $default;
    }

    public static function str_contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}
