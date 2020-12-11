<?php
namespace WptBus\Service\Sale\Consts;


class GoodsConst
{
    //发拍数量限制
    const PUBLISH_ALL_MAX_NUM = 100;
    //上架发拍数量限制
    const PUBLISH_UP_MAX_NUM = 500;
    //上架状态
    const UP_STATUS = 0;
    //下架状态
    const DOWN_STATUS = 1;
    //草稿状态
    const DRAFT_STATUS = 2;

    //per前缀
    const PREFIX = 'G';

    //支付最晚时间
    const PAYMENT_TIME = 48;

    //产品库
    const STANDARD_GOODS_BUSINESS_TYPE_DEPOT = 1;

    //发布好物商品最大数量 类型1 200个
    const PUBLISH_HAO_WU_STANDARD_GOODS_MAX_TYPE1 = [
        2512353,18952063
    ];

    //普通商品
    const STANDARD_TYPE_ORDINARY = 1;
}