<?php

namespace PayCenter\Request\Transfer;

class SystemFrozenRequest extends TransferRequest
{
    const PATH = 'api/v1.0/transfer/system-frozen';

    public function __construct(int $userinfoId, int $money, $content = '{}')
    {
        parent::__construct();
        $this->setUserinfoId($userinfoId)->setMoney($money)->setContent($content);
    }
}
