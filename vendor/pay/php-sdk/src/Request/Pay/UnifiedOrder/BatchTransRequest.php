<?php

namespace PayCenter\Request\Pay\UnifiedOrder;

class BatchTransRequest extends UnifiedOrderRequest
{
    const PATH = 'api/v1.0/pay/batch-trans';

    public function __construct()
    {
        parent::__construct();
        $this->receipts = [];
    }

    /**
     * 增加收款人
     * @param int $userinfoId 收款人ID
     * @param int $money 收款金额（分）
     * @return $this
     */
    public function addReceipt(int $userinfoId, int $money)
    {
        $this->receipts = array_merge($this->receipts, [compact('userinfoId', 'money')]);
        return $this;
    }

    /**
     * 批量设置收款人
     * @param array $receipts [['userinfoId' => 2, 'money' => 100]...]
     * @return $this
     */
    public function setReceipts(array $receipts)
    {
        $this->receipts = $receipts;
        return $this;
    }
}
