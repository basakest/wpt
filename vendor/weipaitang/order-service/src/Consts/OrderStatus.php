<?php

namespace WptOrder\OrderService\Consts;

class OrderStatus
{
    const UNSOLD = -1;           // 流拍
    const DEAL = 1;              // 未付款
    const PAID = 2;              // 付款
    const DELIVERY = 3;          // 发货
    const FINISHED = 4;          // 完成
    const REFUNDING = 5;         // 发起退款
    const REFUNDPAUSE = 6;       // 卖家不同意退款
    const RETURNING = 7;         // 发起退货
    const AGREE_RETURN = 8;      // 同意退货
    const RETURNPAUSE = 9;       // 卖家不同意退货
    const DELIVERY_RETURN = 10;  // 退货已发

    const STATUS_TEXT_MAP = [
        self::UNSOLD => 'unsold',
        self::DEAL => 'deal',
        self::PAID => 'paid',
        self::DELIVERY => 'delivery',
        self::FINISHED => 'finished',
        self::REFUNDING => 'refunding',
        self::REFUNDPAUSE => 'refundpause',
        self::RETURNING => 'returning',
        self::AGREE_RETURN => 'agreeReturn',
        self::RETURNPAUSE => 'returnpause',
        self::DELIVERY_RETURN => 'deliveryReturn',
    ];

}