<?php
/**
 * Created by PhpStorm.
 * User: fangchaogang
 * Date: 2019-04-01
 * Time: 10:25
 */
namespace PayCenter\Response\Bankcard;
use PayCenter\Response\Response;

class QueryListResponse extends Response
{
    /**
     * @return array
     */
    public function getList(): array
    {
        return $this->list;
    }

    public function getCount(): int
    {
        return $this->count;
    }

}