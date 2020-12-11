<?php
/*
 * 未授权操作异常
 *
 * @Author: lin07ux
 * @Created: 2020/7/27 09:35
 */

namespace App\Exceptions;

use Throwable;

class UnauthorizedException extends ApiException
{
    public function __construct($message = "未授权操作", $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
