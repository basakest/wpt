<?php


namespace WptBus\Service\Order\Consts;


class OrderRefundConst
{


    const APPLY = 1;           // 申请退款
    const AGREE = 2;              // 同意退款
    const CLOSE = 3;         // 关闭

    const OrderReturnStatus = [
        'apply' => self::APPLY,
        'agree' => self::AGREE,
        'close' => self::CLOSE,
    ];
}