<?php

namespace WptDataCenter\Route;


class Route
{
    // 数据报表
    const REPORT_LIST = "/dcapi/data-center/inquire";
    // 批量获取数据报表
    const REPORT_BATCH_LIST = "/dcapi/data-center/batch-inquire-fields";
    // 脚本
    const REPORT_COMMAND = "/dcapi/data-center/command";
    // 订阅数据获取
    const SUBSCRIBE_GET = "/dcapi/data-center/get-subscribe-data";
    // 订阅数据批量获取
    const SUBSCRIBE_MUTLI_GET = "/dcapi/data-center/get-subscribe-data-list";
    // 有序集合数据
    const SUBSCRIBE_SORT_DATA_RANK = "/dcapi/data-center/get-subscribe-sort-set-data";
    const BATCH_SUBSCRIBE_SORT_DATA = "/dcapi/data-center/batch-get-subscribe-sort-set-data-by-date";
    // 十五天出过价
    const HAS_BID_15DAY = "/dcapi/data-center/is-has-bid15-day";

    // 名单系统路由
    const EXIST_USER_IN_NAME_LIST = "/dcapi/data-center/inquire-user-name-list";
    const APPEND_NAME_LIST = "/dcapi/data-center/append-name-list";
    const GET_USER_EXPIRE_NAME_LIST = "/dcapi/data-center/user-expire-time-name-list";
    const BUYER_INDEX = "/dcapi/data-center/buyer-order-index";

    // 获取大数据表list
    const BIG_DATA_LIST = "/dcapi/data-center/big-data-list-by-table";
    const DOUBLE_FLOW_VISIT_DEAL = "/dcapi/data-center/double-flow-bring-visit-and-deal";
    const BIG_DATA_GOOD_SHOP_CANDIDTAE = "/dcapi/data-center/good-shop-candidate";
    const BIG_DATA_TODAY_FANS = "/dcapi/data-center/today-increase-fans";
    const BIG_DATA_IS_SCALP = "/dcapi/data-center/is-scalp-sale";
    const BIG_DATA_GOOD_SHOP_VERFIY = "/dcapi/data-center/verify-shop-to-good-shop";
    const BIG_DATA_NEW_SELLER_GROW = "/dcapi/data-center/new-seller-grow";
    const BIG_DATA_SEC_CATEGORY_GMV_SORT = "/dcapi/data-center/sec-category-shop-gmv-sort";
    const BIG_DATA_HISTORY_FANS = "/dcapi/data-center/history-fans-daily";
    const BIG_DATA_SHOP_CAPITAIL_DAISY = "/dcapi/data-center/shop-capital-daily";
    const BIG_DATA_SHOP_PUBLISH_DAILY = "/dcapi/data-center/publish-sale-daily";
    const BIG_DATA_SHOP_CUSTOMER_DAILY = "/dcapi/data-center/customer-daily";
    const BIG_DATA_SHOP_VISIT_DAILY = "/dcapi/data-center/visit-daily";
    const BIG_DATA_SHOP_LIVE_DAILY = "/dcapi/data-center/live-daily";
    const BIG_DATA_SHOP_INVITATION_RANKE_LAST_WEEK = "/dcapi/data-center/shop-invitation-rank-last-week";
    const BIG_DATA_SHOP_INVITATION_RANK = "/dcapi/data-center/shop-invitation-rank";
    const BIG_DATA_SELLER_SERVICE_RATE = "/dcapi/data-center/seller-service-rate";
    const BIG_DATA_SELLER_SERVICE_RATE_LIST = "/dcapi/data-center/seller-service-rate-list";
    const BIG_DATA_USER_DEAL_FAIL = "/dcapi/data-center/user-deal-fail";
    const BIG_DATA_IS_BID_SHARE_LIVE = "/dcapi/data-center/is-bid-or-share-live";
    const BIG_DATA_ORG_CERT_CNT = "/dcapi/data-center/ori-cert-cnt";
    const BIG_DATA_RELATION_UER = "/dcapi/data-center/relation-user";

    // admin

    CONST ADMIN_GET_BUSINESS_NAME_LIST = "/dcapi/data-center-admin/admin-get-business-name-list";
    const ADMIN_EDIT_BUSINESS_NAME_LIST = "/dcapi/data-center-admin/admin-edit-business-name-list";
    const ADMIN_SYNC_BUSINESS_NAME_LIST = "/dcapi/data-center-admin/admin-sync-business-name-list";
    const ADMIN_GET_USER_LIST_BY_BUSINESS = "/dcapi/data-center-admin/admin-get-user-list-by-business";
    const ADMIN_DELETE_USER_FROM_BUSINESS_LIST = "/dcapi/data-center-admin/admin-delete-user-from-business-list";
    const ADMIN_APPEND_BUSINESS_LIST = "/dcapi/data-center-admin/admin-append-business-list";

    const ADMIN_BIG_DATA_MAP_TABLE_LIST = "/dcapi/data-center-admin/admin-big-data-mapping-table-list";
    const ADMIN_EDIT_BIG_DATA_MAP_TABLE = "/dcapi/data-center-admin/admin-edit-big-data-mapping-table";
    const ADMIN_DELETE_BIG_DATA_MAP_TABLE = "/dcapi/data-center-admin/admin-delete-big-data-mapping-table";
    const ADMIN_BIG_DATA_MAP_FIELDS_BY_TABLE = "/dcapi/data-center-admin/admin-big-data-mapping-fields-by-table";
    const ADMIN_EDIT_BIG_DATA_MAP_FIELDS = "/dcapi/data-center-admin/admin-edit-big-data-mapping-fields";
    const ADMIN_DELETE_BIG_DATA_MAP_FIELDS = "/dcapi/data-center-admin/admin-delete-big-data-mapping-fields";
}
