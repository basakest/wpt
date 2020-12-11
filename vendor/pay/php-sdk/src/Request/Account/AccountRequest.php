<?php

namespace PayCenter\Request\Account;

use PayCenter\Request\Request;

abstract class AccountRequest extends Request
{
    //region 账户类型
    //信用账户
    const CREDIT_ACCOUNT = 0;
    //可用余额账户
    const BALANCE_ACCOUNT = 1;
    //冻结余额账户
    const BALANCE_FROZEN_ACCOUNT = 2;
    //临时余额账户
    const TEMP_BALANCE_ACCOUNT = 3;
    //保证金余额账户
    const BZJ_ACCOUNT = 4;
    //提现余额账户
    const WITHDRAW_ACCOUNT = 5;
    //店铺保证金余额账户
    const BAIL_ACCOUNT = 6;
    //待结算余额账户
    const SETTLE_ACCOUNT = 7;
    //货款账户
    const RESIDUE_ACCOUNT = 8;
    //待处理账户
    const ONWAY_ACCOUNT = 9;
    //垫资账户
    const ADVANCE_ACCOUNT = 10;
    //微信合规账户（虚拟）
    const PMC_WEIXIN_ACCOUNT = 11;
    //endregion

    //region 支付类型
    //余额支付
    const PAY_METHOD_TYPE_BALANCE = 1;
    //店铺保证金支付
    const PAY_METHOD_TYPE_BAIL = 2;
    //线下支付
    const PAY_METHOD_TYPE_REMITTANCE = 3;
    //微信支付
    const PAY_METHOD_TYPE_WECHAT = 4;
    //支付宝支付
    const PAY_METHOD_TYPE_ALIPAY = 5;
    //易宝支付
    const PAY_METHOD_TYPE_YEEPAY = 6;
    //连连支付
    const PAY_METHOD_TYPE_LLPAY = 7;
    //qq钱包支付
    const PAY_METHOD_TYPE_QPAY = 8;
    //好友代付
    const PAY_METHOD_TYPE_FRIEND = 9;
    //见证宝
    const PAY_METHOD_TYPE_JZB = 10;
    //微付通
    const PAY_METHOD_TYPE_WFT = 11;
    //货款账户支付
    const PAY_METHOD_TYPE_RESIDUE = 12;
    //农行直连
    const PAY_METHOD_TYPE_ABCHINA = 13;
    //花呗支付
    const PAY_METHOD_TYPE_HUABEI = 14;
    //支付分支付
    const PAY_METHOD_TYPE_PAYSCORE = 15;
    //建行支付
    const PAY_METHOD_TYPE_CCB = 16;
    // 信用支付
    const PAY_METHOD_TYPE_CREDIT = 19;
    //金币（暂只有老数据用）
    const PAY_METHOD_TYPE_COIN = 20;
    //微信合规支付
    const PAY_METHOD_TYPE_WECHAT_PMC = 50;
    //支付方式名称
    const PAY_METHOD_TYPE_NAMES = [
        self::PAY_METHOD_TYPE_BALANCE => '余额',
        self::PAY_METHOD_TYPE_BAIL => '店铺保证金',
        self::PAY_METHOD_TYPE_REMITTANCE => '线下',
        self::PAY_METHOD_TYPE_WECHAT => '微信',
        self::PAY_METHOD_TYPE_ALIPAY => '支付宝',
        self::PAY_METHOD_TYPE_YEEPAY => '银行卡',
        self::PAY_METHOD_TYPE_LLPAY => '银行卡',
        self::PAY_METHOD_TYPE_QPAY => 'QQ钱包',
        self::PAY_METHOD_TYPE_FRIEND => '好友代付',
        self::PAY_METHOD_TYPE_WFT => '微信',
        self::PAY_METHOD_TYPE_JZB => '银行卡',
        self::PAY_METHOD_TYPE_COIN => '金币',
        self::PAY_METHOD_TYPE_RESIDUE => '货款',
        self::PAY_METHOD_TYPE_ABCHINA => '银行卡',
        self::PAY_METHOD_TYPE_HUABEI => '花呗分期',
        self::PAY_METHOD_TYPE_PAYSCORE => '支付分',
        self::PAY_METHOD_TYPE_CREDIT => '信用支付',
        self::PAY_METHOD_TYPE_CCB => '银行卡',
        self::PAY_METHOD_TYPE_WECHAT_PMC => '微信',
    ];
    //endregion

    //region 状态类型
    // 处理完成
    const BALANCE_STATUS_FINISHED = 'finished';
    // 等待处理
    const BALANCE_STATUS_WAIT = 'wait';
    // 银行处理中
    const BALANCE_STATUS_PROCESSING = 'processing';
    // 出错
    const BALANCE_STATUS_ERROR = 'error';
    // 冻结
    const BALANCE_STATUS_FROZEN = 'frozen';
    // 扣款
    const BALANCE_STATUS_DEDUCT = 'deduct';
    // 退款中
    const BALANCE_STATUS_REFUNDING = 'refunding';
    // 部分退款中
    const BALANCE_STATUS_PART_REFUNDING = 'partRefunding';
    // 已退款
    const BALANCE_STATUS_REFUNDED = 'refunded';
    // 已部分退款
    const BALANCE_STATUS_PART_REFUNDED = 'partRefunded';
    // 状态名称
    const BALANCE_STATUS_NAMES = [
        self::BALANCE_STATUS_WAIT => '处理中',
        self::BALANCE_STATUS_PROCESSING => '等待银行入账',
        self::BALANCE_STATUS_ERROR => '失败',
        self::BALANCE_STATUS_DEDUCT => '违约扣除',
        self::BALANCE_STATUS_FINISHED => '成功',
        self::BALANCE_STATUS_FROZEN => '冻结中',
        self::BALANCE_STATUS_REFUNDING => '退款中',
        self::BALANCE_STATUS_PART_REFUNDING => '部分退款中',
        self::BALANCE_STATUS_REFUNDED => '已退款',
        self::BALANCE_STATUS_PART_REFUNDED => '已部分退款'
    ];
    //endregion

    //region 交易类型
    const TRANSACTION_RECHARGE = 'recharge';
    const TRANSACTION_WITHDRAW = 'withdraw';
    const TRANSACTION_INCOME = 'income';
    const TRANSACTION_REFUND = 'refund';
    const TRANSACTION_EXPEND = 'expend';
    const TRANSACTION_DEDUCT = 'deduct';
    //endregion

    public function __construct(int $userinfoId)
    {
        parent::__construct();
        $this->userinfoId = $userinfoId;
    }
}
