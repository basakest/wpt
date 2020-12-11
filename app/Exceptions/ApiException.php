<?php
/**
 *
 * @auther heyu 2020/7/10
 */

namespace App\Exceptions;

use Throwable;

/**
 * 业务异常需要抛出到前端
 * Class ApiException
 * @package App\Exceptions
 */
class ApiException extends \Exception
{
    public function __construct($message = "", $code = 400, Throwable $previous = null)
    {
        $msg = $message;
        parent::__construct($msg, $code, $previous);
    }
}
