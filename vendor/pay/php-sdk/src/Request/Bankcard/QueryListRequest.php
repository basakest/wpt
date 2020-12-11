<?php
/**
 * Created by PhpStorm.
 * User: fangchaogang
 * Date: 2019-04-01
 * Time: 10:09
 */
namespace PayCenter\Request\Bankcard;

use PayCenter\Request\Request;
use PayCenter\Response\Bankcard\QueryListResponse;

class QueryListRequest extends Request
{

    const PATH = 'api/v1.0/bankcard/query-list';

    public function request()
    {
        return new QueryListResponse(parent::request());
    }

    /**
     * @param array $userinfoIds
     * @return $this
     */
    public function setUserinfoIds(array $userinfoIds)
    {
        $this->userinfoIds = $userinfoIds;
        return $this;
    }

    /**
     * @param array $cardNos
     * @return $this
     */
    public function setCardNos(array $cardNos)
    {
        $this->cardNos = $cardNos;
        return $this;
    }
}