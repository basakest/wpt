<?php

namespace PayCenter\Exception;

use PayCenter\Response\Response;

class ResponseException extends Exception
{
    public $response;

    public function __construct(string $message = "", Response $response = null, int $code = Response::UNKOWN_ERROR_CODE)
    {
        parent::__construct($message, $code, null);
        $this->response = $response;
    }
}
