<?php
/**
 * Created by PhpStorm.
 * User: fangchaogang
 * Date: 2019-04-01
 * Time: 10:25
 */
namespace PayCenter\Response\Bankcard;
use PayCenter\Response\Response;
class ToLastUseTimeResponse extends Response
{
    /**
     * 银行卡ID
     * @return int
     */
    public function getBankCardId(): int
    {
        return $this->bankCardId;
    }

    /**
     * @return int
     */
    public function getLastUseTime(): int
    {
        return $this->lastUseTime;
    }

}