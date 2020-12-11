<?php

namespace WptBus\Service\Sale\Consts;

class SaleStatus
{
    const UNSOLD      = -1; // 流拍
    const NOT_PAY_BZJ = 1;  // 未支付拍品保证金
    const SALE        = 2;  // 拍卖中
    const DEAL        = 3;  // 已截拍

    const STATUS_TEXT_MAP = [
        self::UNSOLD      => 'unsold',
        self::NOT_PAY_BZJ => 'notpaybzj',
        self::SALE        => 'sale',
        self::DEAL        => 'deal',
    ];

    const STAUTS_UNSOLD      = 'unsold';    // 流拍
    const STAUTS_NOT_PAY_BZJ = 'notpaybzj'; // 未支付拍品保证金
    const STAUTS_SALE        = 'sale';      // 拍卖中
    const STAUTS_DEAL        = 'deal';      // 已截拍

    // 拍品类型
    const TYPE_NORMAL       = 0;  // 普通，uri以A开头
    const TYPE_RAISE        = 1;  // 抽奖，uri以R开头
    const TYPE_RESERVE      = 2;  // 匠人作品预定（不用）
    const TYPE_FIGHTGROUP   = 3;  // 拼团（不用）
    const TYPE_SEAL         = 4;  // 暗拍
    const TYPE_DONATION     = 5;  // 公益捐款（不用）
    const TYPE_NEW          = 6;  // 新手拍品
    const TYPE_CPS          = 7;  // 镇店宝
    const TYPE_GOOD         = 8;  // 好物
    const TYPE_ZBB          = 9;  // 直播拍
    const TYPE_REDUCTION    = 10; // 降价拍
    const TYPE_UNITARY      = 11; // 一元拍
    const TYPE_TEMPG        = 12; // 直播拍即时一口价
    const TYPE_LIVEGROUPBUY = 13; // 合买
}
