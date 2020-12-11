<?php

namespace PayCenter\Request\Withdraw;

use PayCenter\Request\Request;

class ToWithdrawRequest extends Request
{
    const PATH = 'api/v1.0/withdraw/to-withdraw';

    const WITHDRAW_STATUS_REJECT = -2;
    const WITHDRAW_STATUS_APPROVE = 2;

    /**
     * 审核状态 -2=拒绝，2=审核通过
     *
     * @return static
     */
    public function setStatus(int $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * 审核备注
     *
     * @return static
     */
    public function setRemarks(string $remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * 提现单号
     *
     * @return static
     */
    public function setOrderNo(string $orderNo)
    {
        $this->orderNo = $orderNo;

        return $this;
    }
}
