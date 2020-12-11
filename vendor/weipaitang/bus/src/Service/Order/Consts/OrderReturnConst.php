<?php

namespace WptBus\Service\Order\Consts;

class OrderReturnConst
{
    // 订单操作行为
    // 买家
    const ACTION_BUYER_APPLY_RETURN = "action_buyer_apply_return";
    const ACTION_BUYER_APPLY_REFUND = "action_buyer_apply_refund";
    const ACTION_BUYER_RETURNING = "action_buyer_returning";
    const ACTION_BUYER_CANCEL_WORKER = "action_buyer_cancel_worker";
    const ACTION_BUYER_CANCEL_REFUND = "action_buyer_cancel_refund";
    const ACTION_BUYER_CANCEL_RETURN = "action_buyer_cancel_return";
    const ACTION_BUYER_APPLY_RETURN_REFUND_ONLY = "action_buyer_apply_return_refund_only";

    //卖家
    const ACTION_SELLER_AGREE_RETURN = "action_seller_agree_return";
    const ACTION_SELLER_AGREE_REFUND = "action_seller_agree_refund";
    const ACTION_SELLER_REJECT_REFUND = "action_seller_reject_refund";
    const ACTION_SELLER_REJECT_RETURN = "action_seller_reject_return";
    const ACTION_SELLER_REJECT_RETURN_REFUND_ONLY = "action_seller_reject_return_refund_only";

    //后台
    const ACTION_DASHBOARD_REFUND = "action_dashboard_refund";
    const ACTION_DASHBOARD_REJECT_RETURN = "action_dashboard_cancel_return";
    const ACTION_DASHBOARD_AGREE_RETURN = "action_dashboard_agree_return";
    const ACTION_DASHBOARD_CANCEL_WORKER = "action_dashboard_cancel_worker";

    //系统
    const ACTION_SYSTEM_REFUND_BY_APPLY_TIMEOUT = "action_system_refund_by_apply_timeout";
    const ACTION_SYSTEM_RETURN_BY_APPLY_TIMEOUT = "action_system_return_by_apply_timeout";
    const ACTION_SYSTEM_CANCEL_RETURN = "action_system_cancel_return";
    const ACTION_SYSTEM_AUTO_APPLY_REFUND = "action_system_auto_apply_refund";
    const ACTION_SYSTEM_AUTO_REFUND = "action_system_auto_refund";
    const ACTION_SYSTEM_CANCEL_WORKER = "action_system_cancel_worker";

    //待删除
    const ACTOPN_SELLER_CANCEL_REFUND = "action_seller_cancel_refund";
    const ACTION_DASHBOARD_RETURN_AND_REFUND = "action_dashboard_return_and_refund";


    const RETURN_MESSAGES_MAP = [
        "action_buyer_apply_return" => "买家申请退货退款",
        "action_buyer_apply_refund" => "买家申请退款",
        "action_buyer_returning" => "买家退货已发",
        "action_buyer_cancel_refund" => "买家取消退款",
        "action_buyer_cancel_return" => "买家取消退货",
        "action_buyer_cancel_worker" => "买家取消售后",
        "action_buyer_apply_return_refund_only" => "买家申请退款（无需退货)",

        // 卖家
        "action_seller_agree_return" => "卖家同意退货",
        "action_seller_agree_refund" => "卖家同意退款",
        "action_seller_reject_refund" => "商家拒绝退款，已发货",
        "action_seller_reject_return" => "卖家拒绝退货",
        "action_seller_reject_return_refund_only" => "卖家拒绝仅退款（无需退货）申请",

        // 后台
        "action_dashboard_refund" => "小二工单操作仅退款",
        "action_dashboard_cancel_return" => "小二工单操作拒绝退货",
        "action_dashboard_agree_return" => "小二同意买家退货",
        "action_dashboard_cancel_worker" => "小二取消售后",

        //  系统
        "action_system_refund_by_apply_timeout" => "卖家超时未处理退款申请，系统自动退款",
        "action_system_return_by_apply_timeout" => "卖家超时不处理退货申请，系统同意退货",
        "action_system_cancel_return" => "买家超时未退货，系统自动确认收货",
        "action_system_auto_apply_refund" => "系统自动申请退款",
        "action_system_auto_refund" => "系统自动退款",
        "action_system_cancel_worker" => "系统取消售后",

        // 待删除
        "action_dashboard_return_and_refund" => "小二工单操作退货后退款",
        "action_seller_cancel_refund" => "商家拒绝退款，已发货",
    ];


    const OrderReturnFields = [
        'id',
        'orderId',
        'userinfoId',
        'returnToUserId',
        'returnToAddress',
        'returnToDelivery',
        'returnDeliveryTime',
        'createTime',
        'reasonId',
        'reason',
        'returnStatus',
        'expectRefundFee',
        'remark',
        'returnType',
        'receiveStatus',
        'refundFee',
        'totalFee'
    ];


    const APPLY = 1;           // 申请退货
    const AGREE = 2;              // 同意退货
    const RETURN_DELIVERY = 3;              // 退货发货
    const REFUND_FINISHED = 4;          // 退款已完成
    const REJECT_RETURN = 5;          // 拒绝退货
    const CLOSE = 6;         // 关闭

    const OrderReturnStatus = [
        'apply' => self::APPLY,
        'agree' => self::AGREE,
        'return_delivery' => self::RETURN_DELIVERY,
        'refund_finished' => self::REFUND_FINISHED,
        'reject_return' => self::REJECT_RETURN,
        'close' => self::CLOSE,
    ];




}
