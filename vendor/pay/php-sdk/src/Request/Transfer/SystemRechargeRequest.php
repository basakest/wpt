<?php

namespace PayCenter\Request\Transfer;

use PayCenter\Request\Request;
use PayCenter\Response\TransferResponse;

class SystemRechargeRequest extends Request
{
    const PATH = 'api/v1.0/transfer/system-recharge';

    public function __construct(int $toUserinfoId, int $money, $content = '{}')
    {
        parent::__construct();
        $this->setToUserinfoId($toUserinfoId)->setMoney($money)->setContent($content);
    }

    /**
     * @return TransferResponse
     * @throws \PayCenter\Exception\Exception
     */
    public function request(): TransferResponse
    {
        return new TransferResponse(parent::request());
    }

    /**
     * @param int $toUserinfoId
     * @return static
     */
    public function setToUserinfoId(int $toUserinfoId): self
    {
        $this->toUserinfoId = $toUserinfoId;
        return $this;
    }

    /**
     * @param int $money
     * @return static
     */
    public function setMoney(int $money): self
    {
        $this->money = $money;
        return $this;
    }

    /**
     * @param string $contentJson
     * @return static
     */
    public function setContentJson(string $contentJson): self
    {
        $this->contentJson = $contentJson;
        return $this;
    }

    /**
     * Set the value of content
     *
     * @param mixed $content
     * @return  static
     */
    public function setContent($content): self
    {
        $this->contentJson = is_string($content) ? $content : json_encode($content, JSON_UNESCAPED_UNICODE);
        return $this;
    }

    /**
    * @param string $businessOrderNo
    * @return static
    */
    public function setBusinessOrderNo(string $businessOrderNo): self
    {
        $this->businessOrderNo = $businessOrderNo;
        return $this;
    }
}
