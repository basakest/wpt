<?php

namespace PayCenter\Tests;

use PayCenter\Request\Request;
use PayCenter\Response\Response;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    public function assertResponse(Response $response)
    {
        foreach (get_class_methods($response) as $method) {
            $key = lcfirst(str_replace('get', '', $method));
            if (strpos($method, 'get') === 0 && isset($response->data->$key)) {
                $this->assertEquals(
                    $response->data->$key,
                    $response->$method(),
                    $method . '返回值与原始数据不匹配'
                );
            }
        }
        return $response->data;
    }

    /**
     * @param Request $request
     * @return object
     * @throws \PayCenter\Exception\Exception
     */
    public function assertRequest(Request $request)
    {
        return $this->assertResponse($request());
    }

    public function createId()
    {
        $str = 'abcdefghijklmnopqrstuvwxyz0123456789';
        return date('ymdHi') . substr(str_shuffle($str),0,8);;
    }
}
