<?php

namespace WptBus\Service\Order;

class Router
{
    /**
     * 评价相关
     */
    const SELLE_RRATE_LIST        = "order/seller-rate/get-seller-rate-list";
    const GET_SHOP_RATE_TAGS_COUNT      = "order/seller-rate/get-shop-rate-tags-count";
    const GET_SHOP_RATE_TAGS_LIST = "order/seller-rate/get-shop-rate-tags-list";
    const GET_APPEAL_DETAIL       = "order/seller-rate/get-rate-appeal-detail";
    const GET_SELLER_RATE_DETAIL  = "order/seller-rate/get-seller-rate-detail";
    const BATCH_GET_SELLER_RATE_LIST  = "order/seller-rate/batch-get-seller-rate-list";
    const SAVE_RATE_REPLY         = "order/seller-rate/save-rate-reply";
    const SAVE_RATE_APPEAL        = "order/seller-rate/save-rate-appeal";
    const CANCEL_RATE_APPEAL      = "order/seller-rate/cancel-rate-appeal";

    const GET_WORK_RATE_AMEND_LOG     = "order/order-work/get-amend-log";
    const GET_WORK_RATE_DETAIL        = "order/order-work/get-work-rate-detail";
    const GET_WORK_RATE_LIST          = "order/order-work/get-work-rate-list";
    const SAVE_WORK_RATE              = "order/order-work/save-rate";
    const SET_WORK_RATE_APPEAL_STAUTS = "order/order-work/set-rate-appeal-status";
    const BATCH_GET_SALE_RATE         = "order/order-work/batch-get-sale-rate";

    const GET_BUYER_RATE_INFO    = "order/order-rate/get-rate";
    const ADD_BUYER_RATE_INFO    = "order/order-rate/add-rate";
    const APPEND_BUYER_RATE_INFO = "order/order-rate/append-rate";
    const MODIFY_BUYER_RATE_INFO = "order/order-rate/modify-rate";
    const DELETE_BUYER_RATE_INFO = "order/order-rate/delete-rate";
    const SET_RATE_INVALID       = "order/order-rate/set-rate-invalid";

    const AUTO_RATE                     = "order/order-rate/auto-rate";
    const CLEAR_TIME_END_SALE_RATE_TAGS = "order/order-rate/clear-time-end-sale-rate-tags";

    /**
     * 订单删除
     */
    const RECOVERY_ORDER_BY_BUYER_LIST = "order/order/recovery-order-by-buyer-list";
    const DEL_ORDER_BY_BUYER_LIST      = "order/order/del-order-by-buyer-list";
    const GET_ORDER_BY_URI             = "order/order/get-sale-order-by-uri";

    /**
     * 小红点，订单统计
     */
    const GET_BUYER_ORDER_COUNT  = "order/order/get-buyer-order-count";
    const GET_SELLER_ORDER_COUNT = "order/order/get-seller-order-count";

    /**
     *
     * 发货相关
     */
    const DELIVERY_BATCH_GET_ADDRESS   = 'order/order-delivery/batch-get-order-address';
    const DELIVERY_BATCH_GET_ADDRESS_BY_URI   = 'order/order-delivery/batch-get-order-address-by-uri';
    const DELIVERY_GET_ADDRESS         = 'order/order-delivery/get-order-address';
    const DELIVERY_GET_BUYER_ADDRESS   = 'order/order-delivery/get-buyer-order-address';
    const REST_ORDER_FIRST_DELIVERY_ADDRESS   = 'order/order-delivery/delete-order-first-delivery-address';
    const DELIVERY_BATCH_GET_LOGISTICS = 'order/order-delivery/batch-get-order-logistics';
    const DELIVERY_GET_LOGISTICS       = 'order/order-delivery/get-order-logistics';
    const RELEASE_LOGISTICS_CODE       = 'order/order-delivery/release-logistics-code';

    const TOGETHER_DEVLIERY_LIST  = 'order/order-delivery/get-merge-delivery-list';
    const ORDER_TOGETHER_DEVLIERY = 'order/order-delivery/get-order-merge-delivery-list';
    const DEPOT_ORDER_MERGE_DELIVERY_LIST = 'order/order-delivery/get-depot-order-merge-delivery-list';

    const ORDER_GET_DELAY_DEVLIERY_INFO       = 'order/order-delivery/get-order-delay-delivery';
    const ORDER_BATCH_GET_DELAY_DEVLIERY_INFO = 'order/order-delivery/batch-get-order-delay-delivery';

    const ADD_ORDER_ADDRESS                  = "order/order-delivery/save-order-delivery-address";   // 添加订单地址
    const ADD_ORDER_LOGISTICS                = "order/order-delivery/save-order-delivery-logistics"; // 添加订单物流地址
    const ADD_ORDER_DELAY_DELIVERY           = "order/order-delivery/save-order-delay-delivery";     // 添加订单延时信息
    const CHECK_AND_ADD_ORDER_DELAY_DELIVERY = "order/order-delivery/check-and-save-delay-delivery"; //添加卖家延时发货
    const BATCH_CHECK_ADD_ORDER_DELAY_DELIVERY = "order/order-delivery/batch-check-and-save-delay-delivery"; //添加卖家批量延时发货

    const UPDATE_ORDER_LOGISTICS      = "order/order-delivery/update-order-delivery-logistics"; // 更新订单物流信息
    const BATCH_UPDATE_ORDER_LOGISTICS      = "order/order-delivery/batch-update-order-delivery-logistics"; // 批量更新订单物流信息
    const UPDATE_ORDER_DELAY_DELIVERY = "order/order-delivery/update-order-delay-delivery";     // 更新订单延时信息
    const WIN_DEAL_DELAY_DELIVERY     = "order/order-delivery/win-deal-delay-delivery";         // 买家处理订单延时
    const WIN_BATCH_DEAL_DELAY_DELIVERY     = "order/order-delivery/batch-deal-delay-delivery";         // 买家批量处理订单延时
    const REMAIN_DELIVERY             = "order/order-delivery/remind-delivery";                 // 提醒发货
    const GET_ORDER_REMIND_MAP        = "order/order-delivery/get-order-remind-map";


    const TO_DELIVERY             = "order/order-delivery/to-delivery";             // 发货
    const TO_DELIVERY_WITHOUT_ADDRESS = "order/order-delivery/to-delivery-without-address"; // 发货-无收货地址
    const MULTI_TO_DELIVERY_CHECK = "order/order-delivery/multi-to-delivery-check"; // 发货批量前置检查
    const CHECK_LOGISTICS_CODE    = "order/order-delivery/check-logistics-code";    // 检查物流
    const ORDER_RETURN_LOGISTICS_CHECK    = "order/order-delivery/order-return-logistics-check";    // 退货发货物流校验

    const GET_ORDER_AUCTION_DELIVERY_INFO    = "order/order-delivery/get-order-auction-delivery-info";    // 发货信息[拍品]
    const GET_ORDER_DEPOT_DELIVERY_INFO      = "order/order-delivery/get-order-depot-delivery-info";      // 发货信息[产品库]

    const BIND_ORDER_ADDRESS         = "order/order-delivery/bind-order-address";         // 根据地址ID获取地址详情绑定地址到订单上
    const BIND_ORDER_ADDRESS_BY_INFO = "order/order-delivery/bind-order-address-by-info"; // 根据地址详情绑定地址到订单上
    const REMIND_BUYER_BIND_ADDRESS  = "order/order-delivery/remind-buyer-bind-address";  // 卖家主动提醒买家绑定地址

    const GET_REMIND_BIND_ADD = "order/order-delivery/check-remind-bind-addr-button";// 获取买家绑定地址是否显示按钮

    const EXPORT_ORDER_QUICK_DELIVERY_LIST       = "order/order-quick-delivery/export-order-quick-delivery-list";                                                                                                                                                                                                                                                                                                                                 // 导出数据
    const GET_ORDER_QUICK_DELIVERY_COUNT_BY_USER = "order/order-quick-delivery/get-order-quick-delivery-count-by-user";                                                                                                                                                                                                                                                                                                                           // 统计这个用户有几个订单和可以合并的人
    const GET_ORDER_QUICK_DELIVERY_TOTAL_BY_DATE = "order/order-quick-delivery/get-order-quick-delivery-total-by-date";                                                                                                                                                                                                                                                                                                                           // 按天获取每天快捷发货列表
    const GET_ORDER_QUICK_DELIVERY_LIST          = "order/order-quick-delivery/get-order-quick-delivery-list";                                                                                                                                                                                                                                                                                                                                    // 快捷发货首页按天统计

    const GET_SELLER_ORDER_LIST        = 'order/order/get-seller-order-list';
    const GET_BUYER_ORDER_LIST  = 'order/order/get-buyer-order-list';
    const GET_ORDER_LIST  = 'order/order/get-order-list';
    const GET_SELLER_ORDER_TOTAL = 'order/order/get-seller-order-total';
    const GET_BUYER_ORDER_TOTAL  = 'order/order/get-buyer-order-total';
    const BATCH_GET_ORDER_BY_URI_OR_ID = "order/order/batch-get-sale-order-by-uri-or-id";
    const GET_ORDER_LIST_BY_PID = "order/order/get-order-list-by-pid"; //根据pid查询订单

    // 订单搜索相关
    const SEARCH_BUYER_ORDER_LIST = 'order/order-search/search-buyer-order';                                                                                                                                                                                                                                                                                                                                                                      //搜索订单
    const SEARCH_BUYER_ORDER = 'order/order-search/search-buyer-order-list';                                                                                                                                                                                                                                                                                                                                                                      //搜索订单
    const SEARCH_ORDER_IDS        = "order/order-search/search-order-ids";                                                                                                                                                                                                                                                                                                                                                                        //搜索订单ids
    const SEARCH_SEC_CATEGORY_IDS = "order/order-search/search-sec-category-ids";                                                                                                                                                                                                                                                                                                                                                                 //获取类目ids

    // 创建订单

    const CREATE_ORDER = "order/order/create-sale-order";
    const UPDATE_ORDER = "order/order/update-sale-order";

    //收货相关
    const ORDER_RECEIPT_FIX   = "order/order-receipt/order-receipt-fix";  // 买家确认收货修复接口
    const ORDER_RECEIPT   = "order/order-receipt/order-receipt";  // 买家确认收货
    const APPLY_FACETRADE = "order/order-receipt/apply-face-trade"; //卖家申请当面交易
    const FACETRADE_CHECK = "order/order-receipt/face-trade-check";//当面交易检查
    const DELAY_RECEIPT   = "order/order-receipt/delay-receipt"; // 买家延期收货

    const ADD_DEDUCT_SCORE      = "order/deduct-score/add-deduct-score";      //添加订单扣分
    const GET_DEDUCT_SCORE_LIST = "order/deduct-score/get-deduct-score-list"; // 获取订单扣分记录

    //退款接口
    const APPLY_ORDER_REFUND = "order/order-refund/apply-order-refund"; //申请退款
    const CANCEL_ORDER_REFUND = "order/order-refund/cancel-order-refund"; //取消退款
    const REFUND_ORDER = "order/order-refund/refund-order"; //退款
    const GET_ORDER_REFUND = "order/order-refund/get-order-refund"; //查询退款
    const GET_ORDER_REFUND_LIST = "order/order-refund/get-order-refund-list"; //查询退款列表

    // 退货相关
    const APPLY_ORDER_RETURN = "order/order-return/apply-order-return"; // 申请退货
    const AGREE_ORDER_RETURN = "order/order-return/agree-order-return"; // 同意退货
    const REJECT_ORDER_RETURN = "order/order-return/reject-order-return"; // 拒绝退货
    const TO_DELIVERY_RETURN = "order/order-return/to-delivery-return"; // 退货发货
    const GET_RETURN_ORDER = "order/order-return/get-return-order"; // 获取退货
    const GET_ORDER_HANDLE_RECORD_LIST = "order/order-return/get-order-handle-record-list"; // 获取退货
    const AGREE_RETURN_REFUND = "order/order-return/agree-order-return-refund"; //同意退货退款
    const DASHBOARD_RETURN_REFUND = "order/order-return/dashboard-return-refund";//后台同意退款
    const DASHBOARD_AGREE_ORDER_RETURN = "order/order-return/dashboard-agree-order-return";//后台同意退货
    const DASHBOARD_REJECT_ORDER_RETURN = "order/order-return/dashboard-reject-order-return"; // 拒绝退货
    const CANCEL_RETURN = "order/order-return/cancel-return-order"; // 撤销退货单
    const CREATE_ORDER_RETURN = "order/order-return/create-order-return"; //创建退款单
    const UPDATE_ORDER_RETURN = "order/order-return/update-order-return"; //更新退货单
    const GET_ORDER_RETURN_LIST = "order/order-return/get-return-order-list"; //查询退货单接口

    // 申请恢复订单相关
    const APPLY_ORDER_RESTORED = "order/order-restored/apply-restored";  // 申请恢复订单
    const REJECT_ORDER_RESTORED = "order/order-restored/reject-restored"; // 拒绝恢复订单
    const AGREE_ORDER_RESTORED = "order/order-restored/agree-restored"; // 同意恢复订单
    const CAN_APPLY_RESTORED = "order/order-restored/can-apply-restored"; // 是否可申请恢复订单
    const CAN_OPERATOR_RESTORED = "order/order-restored/can-operator-restored"; // 是否可操作恢复订单
    const CAN_AGREE_RESTORED = "order/order-restored/can-agree-restored"; // 是否可同意恢复订单
    const GET_APPLY_RESTORED_TIME = "order/order-restored/get-has-apply-time"; // 获取已申请恢复订单时间
    const BATCH_GET_HAS_APPLY_RESTORED = "order/order-restored/batch-get-has-apply-restored"; // 批量获取已申请恢复订单

    // 订单列表
    const ORDER_SELLER_LIST = "order/seller/get-seller-list"; // 卖家列表
    const ORDER_BUYER_LIST = "order/buyer/get-buyer-list";  // 买家列表
    const ORDER_BUYER_LIST_DATA = "order/buyer/get-buyer-list-data"; // 通过orderIds查询买家列表信息
}
