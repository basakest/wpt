<?php

namespace WptBus\Service\User;

class Router
{
    const CHECK_SHOP_NAME = "user/verify/check-shop-name";
    const SAVE_SHOP_INFO = "user/verify/save-shopInfo";                                          //保存店铺信息
    const SAVE_PERSONAL_AUTHENTICATION_INFO = "user/verify/save-personal-authentication-info";   //个人认证信息提交
    const SAVE_ENTERPRISE_CERTIFICATION_INFO = "user/verify/save-enterprise-certification-info"; //企业认证信息提交
    const GET_VERIFY_DETAIL = "user/verify/get-verify-detail";                                   //查询认证详细信息
    const GET_APPLY_INFO = "user/verify/get-apply-info";                                         //认证草稿信息
    const REVIEW_APPLY_INFO = "user/verify/review-apply-info";                                   //检查认证申请信息
    const TO_PAY_VERIFY_INFO = "user/verify/to-pay-verify-info";                                 //初始化支付
    const CALL_BACK_DEL_CODIS = "user/verify/call-back-del-codis";                               //支付完成回调更新信息，删除codis
    const GET_VERIFY_CHECK_IS_FAKE = "user/verify/get-verify-info-and-check-is-fake";            //获取认证信息（并检查用户是否存在售假标签）
    const UPDATE_VERIFY_RPUSH_ZX = "user/verify/update-verify-status-and-rpush-zx";              //更新状态到审核中并增加到征信队列
    const GET_USERINFO_VERIFY_LOG_LIST = "user/verify/get-userinfo-verify-log-list";             //认证信息记录列表
    const INSERT_T_USER_VERIFY_INFO = "user/verify/insert-t-user-verify";                        //新增t_user_verify表数据
    const GET_T_USER_VERIFY_INFO = "user/verify/get-t-user-verify";                              //获取首次认证数据
    const CHECK_VERIFY_IS_POLITICS_FAKER = "user/verify/check-verify-is-politics-faker";         //涉政售假校验
    const GET_USERINFO_VERIFY_LIST = "user/verify/get-userinfo-verify-list";                     //认证信息列表
    const GET_SHOP_INFO_LIST = "user/shop-info/get-shop-info-list";                              //分页获取t_shop表数据
    const GET_SHOP_VERIFY_INFO = "user/verify/get-user-verify-info";                             //获取认证信息（迁移user-service sdk）
    const VERIFY_STATUS_TO_REVIEW = "user/verify/verify-status-to-review";                       //更新认证状态（迁移user-service sdk）
    const UPDATE_VERIFY_INFO = "user/verify/update-verify-info";                                 //更新认证信息（迁移user-service sdk）

    /**
     * 店铺装修相关
     */
    const SAVE_SHOP_CUSTOM_PAGE_INFO = "user/shop-decoration/save-shop-custom-page-info";       //保存首页背景、公告、简介信息
    const DEL_SHOP_CUSTOM_PAGE_INFO = "user/shop-decoration/del-shop-custom-page-info";         //删除首页背景、公告、简介信息
    const GET_SHOP_CUSTOM_PAGE_INFO = "user/shop-decoration/get-shop-custom-page-info";         //获取首页背景、公告、简介信息
    const GET_SHOP_DECORATION_BY_SHOP_ID = "user/shop-decoration/get-shop-decoration-by-shop-id"; //根据店铺ID获取包括首页背景、公告、简介三种信息

    /**
     * 店铺设置相关
     */
    const SET_SHOP_RULE      = "user/shop-rule/set-shop-rule";            //店铺设置
    const GET_SHOP_RULE_INFO = "user/shop-rule/get-shop-rule-info";       //获取店铺设置信息


    const CENTER_GET_SOURCE_INFO = "user/center/get-source-info"; //获取用户中心原始数据
    const CENTER_GET_UID_BY_URIS = "user/center/get-uid-by-uris"; // uri => uid
    const CENTER_GET_URI_BY_UIDS = "user/center/get-uri-by-uids"; // uid => uri
    const CENTER_GET_LIST_BY_BINDID = "user/center/get-list-by-bindid";
    const CENTER_GET_LIST_BY_TELEPHONE = "user/center/get-list-by-telephone";
    const CENTER_GET_ONE_BY_UNION_ID = "user/center/get-one-by-union-id";
    const CENTER_GET_UID_TELEPHONE = "user/center/get-uid-by-telephone";

    const PLATFORM_GET_BIND_USER_INFO = "user/platform/get-bind-user-info"; //平台获取绑定用户信息
    const PLATFORM_GET_ALL_USER_INFO = "user/platform/get-all-user-info";   //平台获取所有用户信息

    const PLATFORM_GET_IS_SUBSCRIBE = "user/platform/get-is-subscribe"; //平台获取是否订阅

    /**
     * 星火等级相关
     */
    const SET_USERINFO_SPARK_LEVEL = "user/spark-level/set-userinfo-spark-level";           // 设置商家星火等级接口
    const GET_USERINFO_SPARK_LEVEL_LIST = "user/spark-level/get-userinfo-spark-level-list"; // 批量查询商家星火等级接口
    const GET_UIDS_BY_SPARK_LEVEL = "user/spark-level/get-uids-by-spark-level";             // 根据星火等级获取商家id接口
    const GET_SHOP_SPARK_LEVEL_INFO = "user/spark-level/get-shop-spark-level-info";         // 批量获取店铺星火等级信息

    const AUTH_LOGIN = "user/login/login";
    const AUTH_AUTHENTICATE = "user/login/authenticate";
    const AUTH_REFRESH_TOKEN = "user/login/refresh-token";

    const CODE_SEND = "user/code/send-code";
    const CODE_VERIFY = "user/code/verify";
    const IS_CHECK_IMAGE_CODE = "user/code/is-check-image-code";
    const VERIFY_IMAGE_CODE = "user/code/verify-image-code";
    const CODE_GET_SEND_STATUS = "user/code/get-send-status";

    const INFO_UPDATE_BASE_INFO_BY_SINGLE = "user/info/update-base-info-by-single";
    const INFO_UPDATE_BASE_INFO_BY_ADDRESS = "user/info/update-base-info-by-address";
    const INFO_UPDATE_WECHAT_SUB_INFO = "user/info/update-we-chat-sub-info";
    const INFO_GET_BASE_INFO = "user/info/get-base-info";
    const INFO_GET_BASE_INFO_BATCH = "user/info/get-base-info-batch";
    const INFO_GET_THIRD_INFO = "user/info/get-third-info";
    const INFO_GET_THIRD_INFO_TYPE_BATCH = "user/info/get-third-info-type-batch";
    const INFO_GET_PRIVACY_INFO = "user/info/get-privacy-info";
    const INFO_GET_PRIVACY_INFO_BATCH = "user/info/get-privacy-info-batch";
    const INFO_UPDATE_ID_CARD_INFO = "user/info/update-id-card-info";

    const BIND_TELEPHONE_CHECK = "user/bind-telephone/check";
    const BIND_TELEPHONE_BIND_VERIFY = "user/bind-telephone/bind-verify";
    const BIND_TELEPHONE_BIND_CHANGE = "user/bind-telephone/bind-change";
    const BIND_TELEPHONE_AUTOMATIC_BIND = "user/bind-telephone/automatic-bind";

    /**
     * 缓存迁移新增
     */
    const ADD_CATEGORY_WEIGHT_LIST = "user/shop/add-category-weight-list"; //设置店铺外部加权规则
    const DEL_CATEGORY_WEIGHT_LIST = "user/shop/del-category-weight-list"; //删除店铺外部加权规则
    const GET_ONE_CATEGORY_WEIGHT_LIST = "user/shop/get-one-category-weight-list"; //获取单个店铺外部加权规则
    const GET_ALL_CATEGORY_WEIGHT_LIST = "user/shop/get-category-weight-list"; //获取所有店铺外部加权规则
    const IS_NOVICE_SELLER = "user/seller/is-novice-seller"; //判断卖家是否新手卖家
    const UPDATE_SELLER_INFO_AFTER_PUBLISH = "user/seller/update-seller-info-after-publish"; //更新卖家信息，包括是否新手，7天退订，保证金
    const GET_SHOP_PART_PUBLISH_SETTING = "user/shop/get-shop-part-publish-setting"; //获取店铺部分发布信息


    /**
     * 活体校验
     */
    const USER_VERIFY_CREATE_LIVING_VERIFY_CODE = "user/user-verify-info/create-living-verify-code"; // 创建活体校验码
    const USER_VERIFY_GET_LIVING_VERIFY_CODE = "user/user-verify-info/get-living-verify-code"; // 获取活体校验码
    const USER_VERIFY_CHECK_LIVING_VERIFY_CODE = "user/user-verify-info/check-living-verify-code"; // 校验活体校验码
    const USER_VERIFY_GET_VERIFY_INFO_LIST = "user/user-verify-info/get-verify-info-list"; // 获取认证列表

    /**
     * 持有人校验
     */
    const USER_VERIFY_GET_HOLDER_VERIFY_WAY = "user/user-verify-info/get-holder-verify-way"; // 获取持有人校验方式
    const USER_VERIFY_GET_HOLDER_VERIFY_WAY_DATA = "user/user-verify-info/get-holder-verify-way-data"; // 获取持有人校验方式数据
    const USER_VERIFY_CHECK_HOLDER_VERIFY = "user/user-verify-info/check-holder-verify"; // 校验持有人验证


    // 店铺信息
    const GET_SHOP_INFO = "user/shop-info/get-shop-info";
    const GET_SHOP_DETAIL = "user/shop-info/get-shop-detail";

    // 同步店铺信息的精选相关信息
    const UPDATE_SHOP_RecommendStartAndEndTime = "user/shop-info/update-recommend-time";

    // 同步店铺信息的enterpriseType字段
    const UPDATE_SHOP_EnterpriseType = "user/shop-info/update-enterprise-type";

    // 同步店铺信息的goodshopable (上下优店)
    const UPDATE_SHOP_Goodshopable = "user/shop-info/update-shop-goodshopable";

    // 同步店铺信息的sellerLevelScores (卖家等级积分)
    const UPDATE_SHOP_SellerLevelScores = "user/shop-info/update-shop-sellerlevelscores";

    // 获取新店铺信息（wpt_shop库的t_shop表信息）
    const Get_T_Shop_Info = "user/shop-info/get-t-shop-info";

    // 店铺报表信息
    const GET_SHOP_TODAY_SERVING_REPORT = "user/shop-report/get-today-serving-report";
    const GET_SHOP_DAILY_SERVING_REPORT = "user/shop-report/get-daily-serving-report";

    /**
     * 惩罚相关
     */
    const IS_FORBIDDEN_SHOP = "user/punish/is-forbidden-shop";
    const IS_REDUCE = "user/punish/is-reduce";
    const IS_FORBIIDEN_PUBLISH = "user/punish/is-forbidden-publish";
    const IS_SCALPING = "user/punish/is-scalping";
    const INQUIRE_BATCH_FORBIDDEN_SHOP = "user/punish/inquire-batch-forbidden-shop";
    const INQUIEE_BATCH_REDUCE = "user/punish/inquire-batch-reduce";
    const IS_FORBIDDEN_WITHDRAW = "user/punish/is-forbidden-withdraw";
    const GET_GOODSHOP_WITHOUT_FORBIDDENSHOP = "user/shop/get-good-shop-without-forbidden-shop";
    const BIND_TELEPHONE_BIND_PHONE_PARSE = "user/bind-telephone/bind-phone-parse";
    const BIND_TELEPHONE_BIND_PHONE_PARSE_BY_ONE_CLICK = "user/bind-telephone/bind-phone-parse-by-one-click";

    const BASE_SET_RISK_USER = "user/base/set-risk-user";


    // 玩家社区搜索
    const COMMUNITY_SEARCH_USER = "user/search/community-search-user";
    // 搜索社区用户关注的人
    const COMMUNITY_SEARCH_USER_FOLLOW = "user/search/search-follow-user";
        // 通过企业名称搜索
    const SEARCH_BY_COMPANY_NAME = "user/search/search-by-company-name";
    // 根据店铺名称搜索
    const SEARCH_BY_SHOP_NAME = "user/search/search-by-shop-name";
    // 通过名称(用户名/店铺名)搜索以属性（用户标签）
    const SEARCH_BY_NAME_WITH_PROPERTY = "user/search/search-by-name-with-property";
    // 通过名称(用户名/店铺名)搜索
    const SEARCH_BY_NAME = "user/search/search-by-name";

    
    /**
     * 合并绑定
     */
    const MERGE_TELEPHONE_CHECK = "user/merge/telephone-check";
    const MERGE_TELEPHONE = "user/merge/telephone";
    const MERGE_WE_CHAT_CHECK = "user/merge/we-chat-check";
    const MERGE_WE_CHAT = "user/merge/we-chat";
    const MERGE_WE_CHAT_UN_MERGE = "user/merge/we-chat-un-merge";
    const INFO_UPDATE_USER_TELEPHONE_VERIFY_INFO = "user/info/update-user-telephone-verify-info";


    /**
     * 全站拉黑
     */
    const IS_ALL_BLACK = "user/black/is-all-black";
    const GET_ALL_BLACK_INFO = "user/black/get-all-black-info";
    const BLACK_DO = "user/black/black-do";  // 全站拉黑
    const BLACK_UNDO = "user/black/black-undo";  // 撤销全站拉黑

    /**
     * friend相关
     */
    const GET_FRIEND_ATTENTION_LIST = "user/friend/get-friend-attention-list";  // 关注列表
    const GET_FAN_NUMS = "user/friend/get-fan-nums";                            // 粉丝数
    const TOP_ATTENTION_INFO = "user/friend/top-attention-info";                // 置顶/取消置顶
    const GET_USER_RELATION = "user/relation/get";                              // 关联关系
    const UPDATE_RELATION = "user/relation/incr";                               // 增加支付次数或者违约分
    const GET_USER_RELATION_BY_ID_CODE = "user/verify/get-user-relation-by-id-code";       // 根据个人身份证查询个人关联和认证关联
    const GET_ATTENTION_NUM = "user/friend/get-attention-num";                             // 获取用户关注数量
    const GET_ATTENTION_INFO = "user/friend/get-attention-info";                           // 获取关注信息
    const GET_ATTENTION_INFO_BATCH = "user/friend/get-attention-info-batch";               // 批量获取关注信息
    const GET_ATTENTION_SHOP_ID_ALL_LIST = "user/friend/get-attention-shop-id-all-list";   // 获取所有的关注店铺Id
    const UPDATE_ATTENTION = "user/friend/update-attention";                               // 修改关注信息(关注/取消关注,成为粉丝/取消为粉丝)
    const UPDATE_ATTENTION_BATCH = "user/friend/update-attention-batch";                   // 批量修改关注信息
    const UPDATE_DEAL_NUM = "user/friend/update-deal-num";                                 // 更新成功交易次数（累加）
    const GET_ATTENTION_SHOP_SALE_ID_LIST = "user/friend/get-attention-shop-sale-id-list"; // 获取关注店铺的拍品Id
    const GET_FRIEND_FAN_LIST = "user/fan/get-friend-fan-list"; // 获取粉丝列表
    const SETTING_ISDISTURB = "user/fan/setting-is-disturb"; // 设置消息免打扰
    const GET_ACTIVE_FAN_LIST = "user/fan/get-active-fan-list"; // 获取活跃粉丝列表
    const UPDATE_FAN_ACTIVE_TIME_ASYNC = "user/fan/update-fan-active-time-async"; // 异步更新粉丝活跃时间
    const GET_LIVE_FAN_LIST = "user/fan/get-live-fan-list"; // 获取直播间粉丝列表
    const UPDATE_LIVE_ATTENTION = "user/fan/update-live-attention"; // 直播间关注/取消关注
    const GET_LIVE_ATTENTION_INFO = "user/fan/get-live-attention-info"; // 获取直播间关注状态信息
    const GET_FANS_NUM_BATH = "user/fan/get-fans-num-batch"; // 批量获取粉丝数

    /**
     * 会员相关
     */
    const MEMBER_SYNC_MEMBER_SCORES = "user/member/sync-member-scores"; // 更新会员积分
    const MEMBER_GET_MEMBER_GROWTH_LOG_LIST = "user/member/get-member-growth-log-list"; // 获取会员成长日志列表
    const MEMBER_GET_MEMBER_GROWTH_LOG_LIST_BY_FIELDS = "user/member/get-member-growth-log-list-by-fields"; // 获取会员成长日志列表
    const MEMBER_IS_INCREASED_SCORES = "user/member/is-increased-scores"; // 是否确认收货已经增加过积分
    const MEMBER_OPEN_MEMBER = "user/member/open-member"; // 开通会员

    /**
     * 地址相关
     */
    const GET_ADDRESS = "user/address/get"; // 查询地址信息
    const CREATE_ADDRESS = "user/address/create";// 创建地址信息
    const UPDATE_ADDRESS = "user/address/update";// 更新地址信息
    const DELETE_ADDRESS = "user/address/delete";// 删除地址信息
    const CRATE_OR_UPDATE_ADDRESS = "user/address/create-by-uri";// 通过uri创建或更新地址
    const SET_DEFAULT_SHIPPING_ADDRESS= "user/address/set-default-shipping-address"; //设置默认收货地址
    const SET_DEFAULT_RETURN_ADDRESS = "user/address/set-default-return-address"; //设置默认退货地址
    const GET_DEFAULT_SHIPPING_ADDRESS= "user/address/get-default-shipping-address"; //获取默认收货地址
    const GET_DEFAULT_RETURN_ADDRESS = "user/address/get-default-return-address"; // 获取默认退货地址
    const GET_ADDRESS_LIST = "user/address/get-list"; //地址列表

    /**
     * 账户相关
     */
    const WE_CHAT_CHANGE = "user/merge/we-chat-change"; //微信换绑/绑定
    const APPLY_LOGOUT = "user/logout-apply/apply"; //添加用户注销申请
    const IS_APPLYING_LOGOFF = "user/logout-apply/is-applying"; //基于用户ID查询申请
    const GET_LOGOFF_LIST = "user/logout-apply/apply-list"; //用户注销申请列表
    const REJECT_LOGOFF = "user/logout-apply/reject-apply"; //驳回用户注销申请
    const PASS_LOGOFF = "user/logout-apply/pass-apply"; //通过用户注销申请
    const ACCOUNT_LIST = "user/account/account-list";  // 账户列表
    const ACCOUNT_DETAIL = "user/account/account-detail";  // 账户详情

    /**
     * 登录日志相关
     */
     const GET_LOGIN_LOG_LIST = "user/login-log/login-log-list"; // 登录日志列表

     const MANUAL_ONE_CLICK_VERIFY = "user/user-verify-info/manual-one-click"; // 手动一键校验

    /**
     * 设备ID能力相关
     */
    const GET_DEVICE_BY_UID = "user/device-id-ability/get-device-by-uid"; // 基于用户ID查询设备
    const GET_UID_BY_DEVICE_ID = "user/device-id-ability/get-uid-by-device-id"; // 基于设备ID查询用户ID
    const IS_BOUND_DEVICE = "user/device-id-ability/is-bound-device"; // 是否是绑定过的设备
    const IS_NEW_DEVICE = "user/device-id-ability/is-new-device"; // 是否是新设备

    /**
     * 用户偏好设置相关
     */
    const GET_PREFERENCE = "user/info/get-preference"; // 查询用户偏好设置
    const SET_PREFERENCE = "user/info/set-preference"; // 设置用户偏好设置

    /**
     * 临时迁移,以后需要废弃,为了兼容支付部门对user-go迁移，不要在这里增加功能相关代码
     */
    const GET_LAST_PAY_METHOD = "user/balance/get-last-pay-method"; // 查询最后支付方式
    const UPDATE_LAST_PAY_METHOD = "user/balance/update-last-pay-method"; // 更新最后支付方式
    const UPDATE_BALANCE = "user/balance/update-balance"; // 更新用户余额
    const UPDATE_BAIL = "user/balance/update-bail"; // 更新用户消宝金
    const GET_BNP_JSON = "user/balance/get-bnp-json"; // 获取用户余额免密信息
    const UPDATE_BNP_JSON = "user/balance/update-bnp-json"; // 更新用户余额免密信息
    const UPDATE_SELLER_LEVEL_SCORES = "user/balance/update-seller-level-scores";// 更新卖家积分等级
    const UPDATE_SCENE = "user/balance/update-scene"; // 更新扫码推广渠道
    const UPDATE_IS_ROBOT= "user/balance/update-is-robot"; // 更新是否机器人

    /*
     * 扫码登录相关
     */
    const CREATE_CODE = "user/code-login/create-code"; // 创建code
    const GET_CODE = "user/code-login/get-code";// 查询code状态
    const CHECK_CODE = "user/code-login/check-code";// 校验code
    const CONFIRM_CODE = "user/code-login/confirm-code";// 确认code
    const CANCEL_CODE = "user/code-login/cancel-code";

    /*
     *  风险用户信息
     * */
    const RISK_LIST_GET = "user/risklist/get"; // 查询用户风险信息
    const RISK_LIST_GET_ID_LIST = "user/risklist/get-id-list"; // 查询指定类型风险用户IDs

    /**
     * 用户标签
     */
    const CREATE_TAG = "user/user-tag/create"; // 创建标签
    const UPDATE_TAG = "user/user-tag/update"; // 修改标签
    const DELETE_TAG = "user/user-tag/delete"; // 删除标签
    const CREATE_TAG_GROUP = "user/user-tag/create-group"; // 创建标签组
    const UPDATE_TAG_GROUP = "user/user-tag/update-group"; // 修改标签组
    const DELETE_TAG_GROUP = "user/user-tag/delete-group"; // 删除标签组
    const CHANGE_GROUP_TAG = "user/user-tag/change-group";  // 标签更换分组
    const BATCH_BIND_TAG = "user/user-tag/batch-bind"; // 批量绑定(用户和标签)
    const BATCH_UNBIND_TAG = "user/user-tag/batch-unbind"; // 批量解绑(用户和标签)
    const TAG_LIST_BY_BUSINESS = "user/user-tag/tag-list-by-business-unique-id"; // 分组返回单个业务的所有标签
    const BIND_LIST_BY_ENTITY_ID = "user/user-tag/bind-list-by-entity-id"; // 分组返回实体绑定的标签
    const GET_ENTITY_IDS_BY_TAG_ID = "user/user-tag/get-entity-ids-by-tag-id";  // 获取与标签绑定的实体ID列表
    const SYNC_TAG = "user/user-tag/sync";  // 同步标签
    const SYNC_BATCH_BIND_TAG = "user/user-tag/sync-batch-bind"; // 同步绑定关系(用户和标签)
    const SYNC_BIND_TAGS = "user/user-tag/sync-bind-tags"; // 批量绑定用户的标签
    const SYNC_UNBIND_TAGS = "user/user-tag/sync-unbind-tags"; // 批量解绑用户的标签

    /**
     * 身份证号相关
     * */
    const GET_COUNT_BY_ID_CODE = "user/userinfo/get-count-by-idcode";// 通过身份证号码获取用户数据条数

    /**
     * user extend 相关
     * */
    const GET_USER_EXTEND_BY_UID = "user/extend/get"; // 获取user_extend信息
    const UPDATE_USER_EXTEND = "user/extend/update"; //  更新user_extend里的信息
}
