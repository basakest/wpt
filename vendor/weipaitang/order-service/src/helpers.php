<?php

use WptOrder\OrderService\Consts\SaleStatus;
use WptOrder\OrderService\Consts\OrderStatus;
use WptOrder\OrderService\Consts\UnsoldReason;
use WptOrder\OrderService\Consts\OrderFieldDefaultVal;
use WptOrder\OrderService\Consts\OrderSaleCommonField;
use WptOrder\OrderService\Consts\OrderField;

if (!function_exists('get_sale_status_text')) {

    /**
     * 拍品根据整型状态值获取文本状态值
     * @param int $saleStatus
     * @return string|null
     */
    function get_sale_status_text(int $saleStatus)
    {
        return SaleStatus::STATUS_TEXT_MAP[$saleStatus] ?? null;
    }
}


if (!function_exists('get_sale_status')) {

    /**
     * 拍品根据文本状态值获取整型状态值
     * @param string $saleStatusText
     * @return int|null
     */
    function get_sale_status(string $saleStatusText)
    {
        $statusMap = array_flip(SaleStatus::STATUS_TEXT_MAP);
        return $statusMap[$saleStatusText] ?? null;
    }
}


if (!function_exists('get_all_sale_status')) {

    /**
     * 获取拍品所有整型状态
     * @return array
     */
    function get_all_sale_status(): array
    {
        return array_keys(SaleStatus::STATUS_TEXT_MAP);
    }
}

if (!function_exists('get_all_sale_status_text')) {

    /**
     * 获取拍品所有文本状态
     * @return array
     */
    function get_all_sale_status_text(): array
    {
        return array_values(SaleStatus::STATUS_TEXT_MAP);
    }
}


if (!function_exists('get_order_status_text')) {

    /**
     * 订单根据整型状态值获取文本状态值
     * @param int $saleStatus
     * @return string|null
     */
    function get_order_status_text(int $orderStatus)
    {
        return OrderStatus::STATUS_TEXT_MAP[$orderStatus] ?? null;
    }
}


if (!function_exists('get_order_status')) {

    /**
     * 订单根据文本状态值获取整型状态值
     * @param string $saleStatusText
     * @return int|null
     */
    function get_order_status(string $orderStatusText)
    {
        $statusMap = array_flip(OrderStatus::STATUS_TEXT_MAP);
        return $statusMap[$orderStatusText] ?? null;
    }
}


if (!function_exists('get_all_order_status')) {

    /**
     * 获取订单所有整型状态
     * @return array
     */
    function get_all_order_status(): array
    {
        return array_keys(OrderStatus::STATUS_TEXT_MAP);
    }
}

if (!function_exists('get_all_order_status_text')) {

    /**
     * 获取订单所有文本状态
     * @return array
     */
    function get_all_order_status_text(): array
    {
        return array_values(OrderStatus::STATUS_TEXT_MAP);
    }
}

if (!function_exists('get_unsold_reason')) {

    /**
     * 根据流拍原因文本获取流拍原因整型值
     * @param string $unsoldReasonText
     * @return int|null
     */
    function get_unsold_reason(string $unsoldReasonText)
    {
        $map = array_flip(UnsoldReason::REASON_TEXT_MAP);
        return $map[$unsoldReasonText] ?? null;
    }
}

if (!function_exists('get_unsold_reason_text')) {

    /**
     * 根据流拍原因整型值获取流拍原因文本
     * @param int $unsoldReason
     * @return string|null
     */
    function get_unsold_reason_text(int $unsoldReason)
    {
        return UnsoldReason::REASON_TEXT_MAP[$unsoldReason] ?? null;
    }
}

if (!function_exists('get_all_unsold_reason')) {

    /**
     * 获取所有流拍原因整型值
     * @return array
     */
    function get_all_unsold_reason(): array
    {
        return array_keys(UnsoldReason::REASON_TEXT_MAP);
    }
}


if (!function_exists('get_all_unsold_reason_text')) {

    /**
     * 获取所有流拍原因文本值
     * @return array
     */
    function get_all_unsold_reason_text(): array
    {
        return array_values(UnsoldReason::REASON_TEXT_MAP);
    }
}

if (!function_exists('get_order_and_sale_common_field')) {

    /**
     * 获取拍品和订单共同字段
     * @return array
     */
    function get_order_and_sale_common_field(): array
    {
        return OrderSaleCommonField::COMMON_FIELD;
    }
}

if (!function_exists('get_order_field_default_val')) {

    /**
     * 获取订单字段默认值
     * @return array
     */
    function get_order_field_default_val(string $field)
    {
        return OrderFieldDefaultVal::DEFAULT_VAL[$field] ?? null;
    }
}

if (!function_exists("get_request_info")) {

    /**
     * 获取请求信息
     * @return array
     */
    function get_request_info()
    {
        $info = [
            'uri' => '',
            'query' => '',
        ];

        if (PHP_SAPI == 'cli') {
            $info['uri'] = join(" ", $_SERVER['argv'] ?? []);
        } else if (isset($_SERVER['REQUEST_URI'])) {
            $arr = explode('?', $_SERVER['REQUEST_URI']);
            $info['uri'] = $arr[0];
            $info['query'] = $arr[1] ?? '';
        }

        return $info;
    }
}


if (!function_exists("get_trace_id")) {

    /**
     * 获取 traceId
     * @param bool $rand
     * @return string|null
     */
    function get_trace_id($rand = false)
    {
        $traceId = null;
        if (defined("TRACE_ID")) {
            $traceId = TRACE_ID;
        }

        if (function_exists("molten_get_traceid")) {
            $moltenTraceId = molten_get_traceid();
            if ($moltenTraceId) {
                $traceId = $moltenTraceId;
            }
        }

        if ($traceId === null) {
            $traceId = md5("order-service" . ip2long(current(swoole_get_local_ip()))) . uniqid() . rand(100000, 999999);
        }

        if ($rand) {
            $traceId .= rand(100000, 999999);
        }

        return $traceId;
    }
}

if (!function_exists("chang_array_to_object")) {

    /**
     * 数组转对象化
     * @param $array
     * @return mixed
     */
    function chang_array_to_object($array)
    {
        if (is_array($array)) {
            return json_decode(json_encode($array));
        }

        return $array;
    }

}

if (!function_exists('get_order_field')) {

    /**
     * 获取订单字段默认值
     * @return array
     */
    function get_order_field()
    {
        return OrderField::FIELD;
    }
}
