<?php
/**
 *
 * @auther heyu 2020/7/22
 */

namespace App\Exceptions;

use Throwable;

class UnLoginException extends ApiException
{
    public function __construct($message = "用户未登录", $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
