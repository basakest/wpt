<?php


namespace WptOrder\OrderService\Consts;


class UnsoldReason
{
    const NORMAL = 0;         // 正常流拍
    const NOT_PAY = 1;        // 未付款
    const NOT_DELIVERY = 2;   // 未发货
    const RETURNED = 3;       // 退款
    const REJECT_RECEIPT = 4; // 无保证金拍品当面交易时买家拒绝收货
    const UNPAID_TAIL = 5;    // 未支付尾款

    const REASON_TEXT_MAP = [
        self::NORMAL => 'normal',
        self::NOT_PAY => 'notPay',
        self::NOT_DELIVERY => 'notDelivery',
        self::RETURNED => 'returned',
        self::REJECT_RECEIPT => 'rejectReceipt',
        self::UNPAID_TAIL => 'unpaidTail',
    ];

}