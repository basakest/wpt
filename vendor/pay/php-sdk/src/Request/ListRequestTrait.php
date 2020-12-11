<?php

namespace PayCenter\Request;

use PayCenter\Response\ListResponse;

trait ListRequestTrait
{
    /**
     * @return ListResponse
     * @throws \PayCenter\Exception\Exception
     */
    public function request(): ListResponse
    {
        return new ListResponse(parent::request());
    }

    /**
     * @param string ...$columns
     * @return self
     */
    public function setColumns(string ...$columns)
    {
        $this->columns = implode(',', $columns);
        return $this;
    }

    /**
     * @param string $groupBy
     * @return self
     */
    public function setGroupBy(string $groupBy)
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    /**
     * @param string $orderBy
     * @return self
     */
    public function setOrderBy(string $orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @param string $orderDirection
     * @return self
     */
    public function setOrderDirection(string $orderDirection)
    {
        $this->orderDirection = $orderDirection;
        return $this;
    }

    /**
     * @param int $page
     * @return self
     */
    public function setPage(int $page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @param int $pageSize
     * @return self
     */
    public function setPageSize(int $pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }
}
