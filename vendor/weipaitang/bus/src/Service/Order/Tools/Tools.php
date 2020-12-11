<?php
namespace WptBus\Service\Order\Tools;


use WptBus\Service\Order\Consts\OrderStatus;
use WptBus\Service\Order\Consts\UnsoldReason;

class Tools
{

    /**
     * @param int $unsoldReason
     * @return string|null
     */

    static function getUnsoldReasonText(int $unsoldReason)
    {
        return UnsoldReason::REASON_TEXT_MAP[$unsoldReason] ?? null;
    }


    /**
     * 订单根据整型状态值获取文本状态值
     * @param int $orderStatus
     * @return string|null
     */
    static function getOrderStatusText(int $orderStatus)
    {
        return OrderStatus::STATUS_TEXT_MAP[$orderStatus] ?? null;
    }

    /**
     * 数组转对象化
     * @param $array
     * @return mixed
     */
    static function changArrayToObject($array)
    {
        if (is_array($array)) {
            return json_decode(json_encode($array));
        }

        return $array;
    }
}