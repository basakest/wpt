<?php
/**
 * Created by PhpStorm.
 * User: fangchaogang
 * Date: 2019-04-01
 * Time: 10:09
 */
namespace PayCenter\Request\Bankcard;

use PayCenter\Request\Request;
use PayCenter\Response\Bankcard\QueryResponse;

class QueryRequest extends Request
{

    const PATH = 'api/v1.0/bankcard/query';

    public function request()
    {
        return new QueryResponse(parent::request());
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
     * @param string $type
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
        return $this;
    }
}