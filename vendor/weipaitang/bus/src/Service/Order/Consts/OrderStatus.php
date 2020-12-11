<?php

namespace WptBus\Service\Order\Consts;

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

    const ORDER_STATUS_MAP = [
        'unsold' => self::UNSOLD,
        'deal' => self::DEAL,
        'paid' => self::PAID,
        'delivery' => self::DELIVERY,
        'finished' => self::FINISHED,
        'refunding' => self::REFUNDING,
        'refundpause' => self::REFUNDPAUSE,
        'returning' => self::RETURNING,
        'agreeReturn' => self::AGREE_RETURN,
        'returnpause' => self::RETURNPAUSE,
        'deliveryReturn' => self::DELIVERY_RETURN,
    ];

    const STATUS_TEXT_MAP = [
        self::UNSOLD          => 'unsold',
        self::DEAL            => 'deal',
        self::PAID            => 'paid',
        self::DELIVERY        => 'delivery',
        self::FINISHED        => 'finished',
        self::REFUNDING       => 'refunding',
        self::REFUNDPAUSE     => 'refundpause',
        self::RETURNING       => 'returning',
        self::AGREE_RETURN    => 'agreeReturn',
        self::RETURNPAUSE     => 'returnpause',
        self::DELIVERY_RETURN => 'deliveryReturn',
    ];

    const UNSOLD_NORMAL = 0; //正常流拍
    const UNSOLD_NOT_PAY = 1; //未付款
    const UNSOLD_NOT_DELIVERY = 2; //未发货
    const UNSOLD_RETURNED = 3; //退款
    const UNSOLD_REJECT_RECEIPT = 4; //无保证金拍品当面交易时买家拒绝收货
    const UNSOLD_UN_PAID_TAIL = 5; //未支付尾款

    const ORDER_UNSOLD_REASON_MAP = [
        "normal" => self::UNSOLD_NORMAL,
        "notPay" => self::UNSOLD_NOT_PAY,
        "notDelivery" => self::UNSOLD_NOT_DELIVERY,
        "returned" => self::UNSOLD_RETURNED,
        "rejectReceipt" => self::UNSOLD_REJECT_RECEIPT,
        "unpaidTail" => self::UNSOLD_UN_PAID_TAIL,
    ];
}
