<?php

namespace PayCenter\Request\Transfer;

class TransferForBalanceRequest extends TransferRequest
{
    const PATH = 'api/v1.0/transfer/transfer-for-balance';

    public function __construct(int $userinfoId, int $toUserinfoId, int $money, $content = '{}')
    {
        parent::__construct();
        $this->setUserinfoId($userinfoId)->setToUserinfoId($toUserinfoId)->setMoney($money)->setContent($content);
    }
}
