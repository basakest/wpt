<?php

namespace PayCenter\Response;

class ListResponse extends Response
{
    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
