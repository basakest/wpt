<?php


namespace WptBus\Service\Shop;


class Router
{
    /**
     * 店铺基础信息
     */
    const GET_INFO = "user/shop-info/get-info";
    const BATCH_GET_SHOP_INFO = "user/shop-info/batch-get-shop-info";
    const UPDATE_IS_BRAND = "user/shop-info/update-is-brand";  // 更新品牌馆信息
    const BUSINESS_AUTH = "user/business-auth/auth";  // 鉴权接口
    const INQUIRE_SHOP_BY_PARTS = "user/shop/inquire-shop-by-parts";  // 查询店铺门头信息
    const BATCH_INQUIRE_SHOP_BY_PARTS = "user/shop/batch-inquire-shop-by-parts";  // 批量获取店铺门头信息

    /**
     * 店铺惩罚相关
     */
    const GET_SHOP_PUNISH_INFO = "user/punish/inquire-punish";                     // 获取惩罚信息
    const SET_SHOP_LIMIT_PUBLISH_NUM = "user/punish/set-shop-limit-publish-num";   // 设置店铺发拍限制数
    const GET_SHOP_PUNISH_INFO_LIST_BY_TYPE = "user/punish/get-shop-punish-list-by-type";   // 根据惩罚类型分页获取店铺未过期的惩罚信息列表 （优化Hgetall方式）
    const EXEC_PUNISH = "user/punish/exec-punish";   // 执行惩罚
    const INQUIRE_PUNISH = "user/punish/inquire-punish";   // 查询惩罚信息
    const CANCEL_PUNISH = "user/punish/cancel-punish";   // 取消惩罚
    const FILTER_PUNISH = "user/punish/filter";   // 过滤用户的处罚
    const GET_ALL_PUNISH_BY_TYPE = "user/punish/inquire-all-punish-by-type";   // 查询某个惩罚类型的所有用户

    /**
     * 店铺设置信息
     */
    const SET_SHOP_CONTACT_INFO = "user/shop-rule/set-shop-contact";                // 设置店铺联系人信息
    const GET_SHOP_CONTACT_INFO = "user/shop-rule/get-shop-contact-info";           // 获取店铺联系人信息
    const GET_DELIVERY_COM = "user/extend/delivery-com"; // 获取常用快递
    const SET_DELIVERY_COM = "user/extend/update"; // 设置常用快递

    /**
     * 子账号
     */
    const GET_SUB_ACCOUNT = "user/scope/get-sub-account";   // 获取子账号信息
    const GET_SUB_ACCOUNT_LIST_WITH_UID = "user/scope/get-sub-account-list-with-user-id";                // 获取子账号列表，根据userId
    const GET_SUB_ACCOUNT_LIST_WITH_MASTER_UID = "user/scope/get-sub-account-list-with-master-user-id";  // 获取子账号列表，根据master userId
    const GET_COUNT_SUB_ACCOUNT_WITH_UID = "user/scope/count-sub-account-with-user-id";  // 获取子账号数量，根据userId
    const GET_COUNT_SUB_ACCOUNT_WITH_MASTER_UID = "user/scope/count-sub-account-with-master-user-id";  // 获取子账号数量，根据master userId
    const CREATE_SUB_ACCOUNT = "user/scope/create-sub-account";  // 创建子账号
    const UPDATE_SUB_ACCOUNT = "user/scope/update-sub-account";  // 更新子账号
    const GET_SUB_ACCOUNT_LIST_WITH_UID_ALL = "user/scope/get-sub-account-list-with-user-id-all";  // 获取是永久的子账号

    /**
     * 店铺标签
     */
    const GET_SHOP_TAG = "user/tag/get-user-tag";                          // 获取店铺标签
    const GET_SHOP_TAG_LIST = "user/tag/get-user-tag-list";                // 获取店铺标签列表
    const UPDATE_SHOP_TAG = "user/tag/replace-user-tag";                   // 更新店铺标签
    const GET_RATE_TAG_LIST = "user/rate-tag/get-user-rate-tag-list";      // 获取店铺评价标签

    /**
     * 拉黑
     */
    const GET_BLACK_LIST = "user/black/get-black-list";                          // 拉黑列表
    const GET_BLACK_NUMS = "user/black/get-black-count";                         // 拉黑数
    const GET_BE_BLACK_LIST = "user/black/get-be-black-list";                    // 被拉黑列表
    const UPDATE_BLACK = "user/black/update-black";                              // 拉黑/取消拉黑
    const GET_STANDARD_GOODS_BLACK = "user/shop/get-standard-goods-publish-black";   // 一口价黑名单


    /**
     * 店铺报表
     */
    const GET_SHOP_REPORT = "user/shop-report/inquire";                          // 查询店铺报表基础属性信息
    const GET_TODAY_CAPITAL = "user/shop-report/today-capital";                  // 店铺今日的资金报表
    const GET_TODAY_PUV = "user/shop-report/today-puv";                          // 店铺的pv和uv
    const GET_TODAY_PUBLISH = "user/shop-report/today-publish";                  // 店铺今日上拍数
    const GET_TODAY_BUYER_AND_NEW_CUSTOMER = "user/shop-report/today-buyer-and-new-customer";    // 店铺的今日付款人数和新客占比


}