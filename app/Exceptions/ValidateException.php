<?php
/**
 *
 * @auther heyu 2020/7/9
 */

namespace App\Exceptions;

class ValidateException extends ApiException
{
    public function __construct($message = "", $code = 422, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
