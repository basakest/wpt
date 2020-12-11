<?php

namespace WptBus\Service\Recommend;

class Router
{
    /**
     * 店铺新品消息接口
     */
    const POST_SHOP_MESSAGE        = "da-api-shop-message/shop-message/post-messages"; // 提交店铺新品消息
    const GET_SHOP_MESSAGES      = "da-api-shop-message/shop-message/get-messages-by-user"; // 获取店铺新品消息列表
}
