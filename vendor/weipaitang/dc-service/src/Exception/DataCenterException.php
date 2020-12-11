<?php

namespace WptDataCenter\Exception;


use Throwable;

class DataCenterException extends \RuntimeException
{
    public function __construct(string $message = "data-center error", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
