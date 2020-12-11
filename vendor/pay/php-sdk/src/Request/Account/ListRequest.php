<?php

namespace PayCenter\Request\Account;

class ListRequest extends AccountRequest
{
    const PATH = 'api/v1.0/account/list';

    /**
     * @param mixed $accountType
     * @return ListRequest
     */
    public function setAccountType(...$accountType): ListRequest
    {
        $this->accountType = implode(',', $accountType);
        return $this;
    }

    /**
     * @return array
     * @throws \PayCenter\Exception\Exception
     */
    public function request()
    {
        return array_column((array)parent::request()->items, 'money', 'type');
    }
}
