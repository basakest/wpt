<?php

namespace WptOrder\OrderService\Consts;

class SaleStatus
{
    const UNSOLD = -1;   // 流拍
    const NOTPAYBZJ = 1; // 等待支付保证金
    const SALE = 2;      // 拍卖中
    const DEAL = 3;      // 成交

    /**
     * 状态值 => 状态值对应的字符串表示
     */
    const STATUS_TEXT_MAP = [
        self::UNSOLD => 'unsold',
        self::NOTPAYBZJ => 'notpaybzj',
        self::SALE => 'sale',
        self::DEAL => 'deal',
    ];
}