<?php

namespace WptBus\Service\Sale;

class Router
{
    const GET_SALE_COMPONENT_LIST_BY_IDS = "sale/sale/get-format-sale-list-by-ids";

    const GET_SALE_COMPONENT_LIST_BY_URIS = "sale/sale/get-format-sale-list-by-uris";

    const GET_SALE_COUNT = "sale/sale/get-sale-count";

    const GET_STANDARD_GOODS_COUNT = "sale/standard-goods/get-count";

    const GET_STANDARD_GOODS_BY_ID = "sale/standard-goods/get-by-id";

    const GET_STANDARD_GOODS_MULTI_GET_BY_IDS = "sale/standard-goods/multi-get-by-ids";

    const STANDARD_GOODS_UPDATE = "sale/standard-goods/update";

    const STANDARD_GOODS_DELETE = "sale/standard-goods/delete";

    const STANDARD_GOODS_URI_2_ID = "sale/standard-goods/uri-2-id";

    const STANDARD_GOODS_UPDATE_STOCK = "sale/standard-goods/update-stock";

    const GET_USER_SALE_LIST = "sale/sale/get-user-sale-list";

    const BID_TO_BID = 'sale/bid/to-bid'; // 出价

    const BID_INFO = 'sale/bid/info';  // 出价聚合查询

    const BID_HAS_BID = 'sale/bid/has-bid';

    const BID_HAS_HISTORY_BID = 'sale/bid/has-history-bid';

    const BID_HAS_SALE_BID = 'sale/bid/has-sale-bid';

    const BID_INSERT_BID = 'sale/bid/insert-bid';

    const GET_TOP_PRICE_AND_BID_NUM = "sale/bid/get-top-price-and-bid-num";

    const GET_SALE_BID_COUNT = "sale/bid/get-sale-bid-count";

    const GET_MAX_PRICE = "sale/bid/get-max-price";

    const GET_SALE_BID_LIST = "sale/bid/get-sale-bid-list";

    // 拍品出价列表（带聚合数据）
    const GET_SALE_BID_DETAIL_LIST = "sale/bid/get-sale-bid-detail-list";

    // 拍品详情页定制化聚合接口
    const GET_SALE_INFO_AGGREGATION = "sale/sale/get-sale-info-aggregation";

    const INSERT_SALE = "sale/sale/insert-sale";

    const UPDATE_SALE = "sale/sale/update-sale";

    const BID_GET_7_DAY_SALE_COUNT = "sale/bid/get7-day-sale-count";

    const BID_BATCH_GET_MAX_PRICE = "sale/bid/batch-get-max-price";

    const BID_BATCH_GET_SALE_BID_COUNT = "sale/bid/batch-get-sale-bid-count";

    const BID_BATCH_GET_SALE_BID_LIST = "sale/bid/batch-get-sale-bid-list";

    // 围观（点赞）相关接口
    const IS_LIKE_SALE = "sale/sale-like/is-like-sale";

    const GET_SALE_LIKE_LIST = "sale/sale-like/get-sale-like-list";

    const GET_7DAY_LIKE_SALE_LIST = "sale/sale-like/get-7-day-sale-list";

    const GET_LIKE_SALE_LIST = "sale/sale-like/get-like-sale-list";

    const GET_7DAY_LIKE_SALE_COUNT = "sale/sale-like/get-7-day-sale-count";

    const GET_LIKE_SALE_LIST_BY_CATEGORY = "sale/sale-like/get-like-sale-list-by-category";

    const LIKE_TO_LIKE = 'sale/sale-like/to-like'; // 去围观

    const LIKE_CANCEL_LIKE = 'sale/sale-like/cancel-like-new'; // 新取消围观接口

    // 参拍列表接口
    const GET_BID_SALE_LIST = "sale/bid/get-bid-sale-list";

    const GET_SALE_WITH_BID = "sale/sale/get-sale-with-order";

    // 逛逛 discovery
    const GET_DISCOVERY_SALE_LIST = 'sale/discovery/get-data';

    // 根据拍品id删除围观信息
    const DELETE_BY_SALE_ID = "sale/sale-like/delete-by-sale-id";

    const GET_SALE_MANAGE_LIST = "sale/sale/get-sale-manage-list";

    // 拍品详情页
    const GET_SALE_DETAIL = 'sale/sale-detail/index';

    const TO_SALE_DEL = "sale/sale/to-sale-del";

    const MULTI_GET_SALE = "sale/sale/multi-get-sale";

    const GET_SEVEN_DAYS_BID_SALE_LIST = "sale/bid/get-seven-days-bid-sale-list";

    const MULTI_GET_SALE_INFO = 'sale/sale/multi-get-sale-info';

    // 暗拍发拍
    const SEAL_SALE_PUBLISH = "sale/sale/seal-sale-publish";

    const GET_USER_BID_SALE_LIST = "sale/bid/get-user-bid-sale-list";

    const BID_BATCH_INFO = "sale/bid/batch-info";

    // 更新拍品可鉴定标签
    const TO_UPDATE_ENABLE_IDENT_TAG = "sale/sale/to-update-enable-ident-tag";

    // 同步拍出价
    const SYNC_AUCTION_TO_BID = 'sale/bid/sync-auction-to-bid';
    // 出价撤回
    const BID_BACK_OFF = 'sale/bid/bid-back-off';

    const INSERT_SUB_SALE = 'sale/sale/insert-sub-sale';

    const UPDATE_SUB_SALE = 'sale/sale/update-sub-sale';

    const GET_IN_SALE_USER_BID_SALE_LIST = "sale/bid/get-in-sale-user-bid-sale-list";

    const GET_IN_DEAL_USER_BID_SALE_LIST = "sale/bid/get-in-deal-user-bid-sale-list";
    /* -------------------- 更新 -------------------- */
    // 普通发拍 获取草稿箱
    const NORMAL_GET_DRAFT = 'sale/draft/add-normal-verify';
    // 产品库批量发拍
    const BATCH_PUBLISH_GOODS = 'sale/draft/batch-publish-goods';
    // 产品库批量发拍
    const BATCH_PUBLISH_SALE = 'sale/normal/batch-publish';
    // 普通发拍 插入草稿箱
    const NORMAL_INSERT_DRAFT = 'sale/draft/add-normal-draft';
    // 普通拍品发拍验证
    const NORMAL_GET_SALE = 'sale/normal/sale-publish-verify';
    // 普通发拍 插入sale
    const NORMAL_INSERT_SALE = 'sale/normal/normal-sale-publish';
    // 重新发拍
    const NORMAL_SALE_REPUBLISH = 'sale/normal/to-sale-republish';
    // 直播间快速发拍
    const LIVE_SALE_QUICK_PUBLISH = 'sale/live/quick-sale-publish';
    // 一口价 插入草稿箱
    const STANDARD_INSERT_DRAFT = 'sale/standard-goods/add-goods';
    // 一口价 获取发拍设置
    const STANDARD_GET_SALE = 'sale/standard-goods/sale-verify';
    // 一口价 插入sale
    const STANDARD_INSERT_SALE = 'sale/standard-goods/sale-publish';
    //一口价 草稿箱验证
    const STANDARD_GET_DRAFT = 'sale/standard-goods/goods-verify';

    const HAS_USER_BID_IN_SALE = 'sale/bid/has-user-bid-in-sale';

    const GET_PUBLISH_COUNT = 'sale/sale/get-publish-count';

    const BATCH_GET_SALE_EXPRESS_FEE_TEMPLATE = "sale/sale/batch-get-sale-express-fee-template";

    const GET_SALE_EXPRESS_FEE_TEMPLATE_LIST = "sale/sale/get-sale-express-fee-template-list";

    const BATCH_GET_SALE_EXPRESS_FEE_TEMPLATE_LIST = "sale/sale/batch-get-sale-express-fee-template-list";

    const INSERT_SALE_EXPRESS_FEE_TEMPLATE = "sale/sale/insert-sale-express-fee-template";

    const UPDATE_SALE_EXPRESS_FEE_TEMPLATE = "sale/sale/update-sale-express-fee-template";

    const BATCH_GET_BRAND = 'sale/brand/batch-get-brand';

    const CREATE_BRAND = 'sale/brand/create-brand';

    const UPDATE_BRAND = 'sale/brand/update-brand';

    const GET_BRAND_LIST = 'sale/brand/get-brand-list';

    const GET_BRAND_COUNT = 'sale/brand/get-brand-count';

    const GET_BRAND_CATEGORY_RELATION_LIST = "sale/brand/get-brand-category-relation-list";

    const CREATE_BRAND_CATEGORY_RELATION = "sale/brand/create-brand-category-relation";

    const UPDATE_BRAND_CATEGORY_RELATION  = "sale/brand/update-brand-category-relation";

    const DELETE_BRAND_CATEGORY_RELATION  = "sale/brand/delete-brand-category-relation";

    const GET_BRAND_CATEGORY_RELATION_COUNT = "sale/brand/get-brand-category-relation-count";

    // 一口价创建
    const CREATE_GOODS = 'sale/goods/create';
    const UPDATE_GOODS = 'sale/goods/update';
    const SET_STOCK = 'sale/goods/updateStock';
    const INCR_VIEW = 'sale/goods/incr-view';
    const GET_GOODS_LIST = 'sale/goods/get-goods-list';
    const GET_GOODS = 'sale/goods/get-goods';
    const GOODS_URI_2_ID = 'sale/goods/uri-2-id';
    const GET_GOODS_COUNT = 'sale/goods/get-count';
    const MULTI_GET_BY_IDS = 'sale/goods/multi-get-goods';

    // 根据参数（天内），查询是否出过价
    const BID_HAS_BID_BY_DAY = 'sale/bid/is-has-bid-by-day';

    const URI_2_SALE_ID = "sale/sale/uri-2-sale-id";

    const SALE_ID_2_URI = "sale/sale/sale-id-2-uri";

    const SEARCH_SALE_LIST = "sale/sale/search-sale-list";

    // 微拍联盟
    const UNION_STANDARD_GOODS_PUBLISH = 'sale/sale/union-standard-goods-publish';

    // ---------- 作者相关
    const AUTHOR_GET_LIST = 'sale/sale-author/get-list';
    const AUTHOR_GET_COUNT = 'sale/sale-author/get-author-count';
    const AUTHOR_UPDATE_STATUS = 'sale/sale-author/update-status';
    const AUTHOR_CREATE_AUTHOR = 'sale/sale-author/create-author';
    const AUTHOR_GET_AUTHOR_IDENTITY = 'sale/sale-author/get-author-identity';

    // 批量获取最高价和出价次数
    const MULTI_GET_TOP_PRICE_AND_BID_NUM = "sale/bid/multi-get-top-price-and-bid-num";

    // sale
    const SALE_INCR_VIEW = 'sale/sale/incr-view';
    const SALE_GET_COUNT = 'sale/sale/get-count';
    const SALE_GET_PUSH_SALE_LIST = 'sale/sale/get-push-sale-list';
    const SALE_USER_SHOP_NEW_SALE_LIST = 'sale/sale/user-shop-new-sale-list';
    const SALE_GET_SHOP_CATEGORY_SALES = 'sale/sale/get-shop-category-sales';
    const SALE_GET_USER_SALE_LIST = 'sale/sale/get-user-sale-list';
    const SALE_GET_ON_SALE_LIST_BY_WHERE = 'sale/sale/get-on-sale-list-by-where';
    const SALE_GET_ON_SALE_LIST_BY_USER_INFO_IDS = 'sale/sale/get-on-sale-list-by-user-info-ids';
    const SALE_GET_ON_SALE_LIST_BY_DEPOT_ID = 'sale/sale/get-on-sale-list-by-depot-id';
    const SALE_GET_ON_SALE_LIST_BY_PD_ID = 'sale/sale/get-on-sale-list-by-pd-id';
    const SALE_GET_ON_SALE_LIST_BY_DEPOT_USER_ID = 'sale/sale/get-on-sale-list-by-depot-user-id';
    const SALE_GET_NOT_PAY_BZJ_SALE_BY_DRAFT_ID = 'sale/sale/get-not-pay-bzj-sale-by-draft-id';
    const SALE_GET_SALE_BY_DRAFT_ID = 'sale/sale/get-sale-by-draft-id';
    const SALE_GET_SALE_LIST_BY_DRAFT_ID = 'sale/sale/get-sale-list-by-draft-id';
    const SALE_GET_SHOP_DETAIL_OF_SALE = 'sale/sale/get-shop-detail-of-sale';
    const SALE_QUERY_IMG_MD5 = 'sale/sale/QueryImgMd5';

    // 类目相关
    const GET_CHILD_CATEGORY = "sale/category/get-child-category";
    const GET_CATEGORY_TREE = "sale/category/get-category-tree";

    // draft
    const DRAFT_GET_DRAFT = 'sale/draft/get-draft';
    const DRAFT_UPDATE_DRAFT = 'sale/draft/update-draft';
    const DRAFT_INSERT_DRAFT = 'sale/draft/insert-draft';
    const DRAFT_BATCH_INSERT_DRAFT = 'sale/draft/batch-insert-goods';
    const DRAFT_DELETE_DRAFT = 'sale/draft/delete-goods';
    const DRAFT_MULTI_DELETE_GOODS = 'sale/draft/multi-delete-goods';
    const DRAFT_GET_DRAFT_LIST = 'sale/draft/get-draft-list';
    const DRAFT_GET_UNITARY_LIST = 'sale/draft/get-unitary-list';
    const DRAFT_UNION_GET_DRAFT_LIST = 'sale/draft/union-get-draft-list';

    // 名家精选
    const MASTER_GET_MASTER_SALE_IDS = 'sale/sale/get-master-sale-ids';
    const MASTER_SET_TOP_MASTER_SALE = 'sale/sale/set-top-master-sale';
    const MASTER_GET_SALE_LIST_BY_MASTER_ID = 'sale/sale/get-sale-list-by-master-id';
    const MASTER_GET_TOP_MASTER_SALE_LIST = 'sale/sale/get-top-master-sale-list';
    const MASTER_GET_MASTER_SALE_LIST = 'sale/sale/get-master-sale-list';
    const MASTER_SET_MASTER_SALE_INFO = 'sale/sale/set-master-sale-info';
    const MASTER_GET_PREV_NEXT_MASTER_SALE = 'sale/sale/get-prev-next-master-sale';
    const MASTER_SEARCH_MASTER_SALES = 'sale/sale/search-master-sales';
    const MASTER_SET_MASTER_SALE_LIST = 'sale/sale/set-master-sale-list';
    const MASTER_GET_MASTER_SALE_INFO = 'sale/master-sale/get-master-sale-info';

    // recommend
    const SALE_GET_RECOMMEND_SALE_LIST = 'sale/recommend/get-recommend-sale-list';
    const SALE_GET_SHOP_RECOMMEND_SALE_LIST = 'sale/recommend/get-shop-recommend-sale-list';
    const SALE_MANAGE_SHOP_RECOMMEND_SALE_LIST = 'sale/recommend/manage-shop-recommend-sale-list';
    const SALE_RECOMMEND_TOGETHER = 'sale/recommend/recommend-together';
    const SALE_SET_RECOMMEND_SALE = 'sale/recommend/set-recommend-sale';

    // discovery
    const DISCOVERY_GET_DATA = 'sale/discovery/get-data';
    const DISCOVERY_GET_DATA_GUARANTEED_RECOMMEND_TOGETHER = 'sale/discovery/get-data-guaranteed-recommend-together';

    // search
    const SEARCH_SEARCH_EXTENDED_WORDS = 'sale/sale/search-extended-words';
    const SEARCH_SEARCH_KEYWORD = 'sale/sale/search-keyword';
    const SEARCH_GET_TAG_SEARCH_LIST = 'sale/sale/get-tag-search-list';
    const SEARCH_GET_CATEGORY_SEARCH_LIST = 'sale/sale/get-category-search-list';

    // admin publisher
    const ADMIN_PUBLISHER_SALE_PUBLISH = 'sale/sale/sale-publish';
    const ADMIN_PUBLISHER_STANDARD_GOODS_PUBLISH = 'sale/sale/standard-goods-publish';
}
