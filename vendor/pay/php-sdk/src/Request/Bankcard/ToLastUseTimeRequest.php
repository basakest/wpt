<?php
/**
 * Created by PhpStorm.
 * User: fangchaogang
 * Date: 2019-04-01
 * Time: 17:58
 */
namespace PayCenter\Request\Bankcard;
use PayCenter\Request\Request;
use PayCenter\Response\Bankcard\ToLastUseTimeResponse;

class ToLastUseTimeRequest extends Request
{
    const PATH = 'api/v1.0/bankcard/to-last-use-time';

    public function request()
    {
        return new ToLastUseTimeResponse(parent::request());
    }

    /**
     * @param int $userinfoId
     * @return $this
     */
    public function setUserinfoId(int $userinfoId)
    {
        $this->userinfoId = $userinfoId;
        return $this;
    }

    /**
     * @param int $bankCardId
     * @return $this
     */
    public function setBankCardId(int $bankCardId)
    {
        $this->bankCardId = $bankCardId;
        return $this;
    }

    /**
     * @param int $lastUseTime
     * @return $this
     */
    public function setLastUseTime(int $lastUseTime)
    {
        $this->lastUseTime = $lastUseTime;
        return $this;
    }
}