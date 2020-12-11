<?php

namespace PayCenter\Request\Withdraw;

use PayCenter\Request\Request;
use PayCenter\Response\Withdraw\InfoResponse;

class InfoRequest extends Request
{
    const PATH = 'api/v1.0/withdraw/info';

    //提现表状态-驳回（已退款）
    const STATUS_REJECT = -2;
    //提现表状态-失败（未退款）
    const STATUS_FAIL = -1;
    //提现表状态-初始化（未扣款）
    const STATUS_INIT = 0;
    //提现表状态-待处理（已扣款）
    const STATUS_WAITING = 1;
    //提现表状态-等待入账中（已发起第三方转账）
    const STATUS_PROCESSING = 2;
    //提现表状态-成功（第三方回调成功）
    const STATUS_SUCCESS = 3;

    //提现方式
    const WITHDRAW_METHOD_BALANCE = 1;
    const WITHDRAW_METHOD_WECHAT = 2;
    const WITHDRAW_METHOD_BANKCARD = 4;

    public function __construct(string $orderNo)
    {
        parent::__construct();
        $this->orderNo = $orderNo;
    }

    public function request(): InfoResponse
    {
        return new InfoResponse(parent::request());
    }
}
