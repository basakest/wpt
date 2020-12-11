<?php

namespace PayCenter\Exception;

class RequestException extends Exception
{
    public $url;
    public $request;

    public function __construct(string $message, string $url, array $request)
    {
        parent::__construct($message, 0, null);
        $this->request = $request;
        $this->url = $url;
    }
}
