<?php

namespace WptBus\Service\Sale\Consts;

class SaleExpressFeeTemplate
{
    // 类型
    const SALE_TYPE_SALE           = 1; // 拍品
    const SALE_TYPE_STANDARD_GOODS = 2; // 标品
    const SALE_TYPE_LIVE_GOODS     = 3; // 直播间一口价

    // 状态
    const SALE_STATUS_NO_BID  = 1; // 未出价
    const SALE_STATUS_HAS_BID = 2; // 已出价
    const SALE_STATUS_DEAL    = 3; // 已截拍
    const SALE_STATUS_UNSOLD  = 4; // 已流拍
    const SALE_STATUS_DELETE  = 5; // 已删除
}
