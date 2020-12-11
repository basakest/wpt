<?php


namespace App\Exceptions;

use Throwable;

/**
 * 资源未找到
 * Class ResourceNotFoundException
 * @package App\Exceptions
 */
class ResourceNotFoundException extends ApiException
{
    /**
     * 创建资源未找到异常
     *
     * @param string $message
     * @param Throwable $previous
     */
    public function __construct($message, Throwable $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }

}