<?php

namespace PayCenter\Response;

use JsonSerializable;
use PayCenter\Config;
use PayCenter\Signature;
use PayCenter\Request\Request;
use PayCenter\Exception\{ResponseException, ApiException, SignatureException};

class Response implements JsonSerializable
{
    const SUCCESS_CODE = 0;
    const SIGNATURE_ERROR_CODE = 131;
    const UNKOWN_ERROR_CODE = -99;

    public $request;
    public $original;
    public $data;

    /**
     * Response constructor.
     * @param mixed $original 原始返回数据
     * @param Request|null $request
     * @throws \PayCenter\Exception\Exception
     */
    public function __construct($original, Request $request = null)
    {
        switch (true) {
            case $original instanceof Response:
                foreach (get_object_vars($original) as $key => $value) {
                    if (property_exists($this, $key)) {
                        $this->$key =& $value;
                    }
                }
                break;
            default:
                if (empty($original)) {
                    throw new ResponseException('支付中心返回结果为空');
                }

                $this->request = $request;
                $this->original = $original;
                $this->parseOriginal();

                if (!Signature::check((array)$this->data, Config::getKey())) {
                    throw new SignatureException('支付接口返回签名校验失败', $this, self::SIGNATURE_ERROR_CODE);
                }
        }
    }

    /**
     * 解析原始数据
     * @throws ResponseException
     */
    protected function parseOriginal()
    {
        $response = json_decode($this->original);
        if (empty($response)) {
            throw new ResponseException('支付中心返回数据异常', $this);
        }

        $code = $response->code ?? self::UNKOWN_ERROR_CODE;
        $msg = $response->msg ?? '支付中心处理异常';

        if ($code != self::SUCCESS_CODE) {
            switch (true) {
                case $code == self::SIGNATURE_ERROR_CODE:
                    throw new SignatureException('支付接口请求签名校验失败', $this, $code);
                case $code <= 200: //缺少参数，参数类型校验等异常
                    //throw new ResponseException($msg, $this, $code);
                default:
                    throw new ApiException($msg, $this, $code);
            }
        }

        $this->data = $response->data;
    }


    public function data()
    {
        $data = (array)$this->data;
        if(isset($data['randomStr'])) unset($data['randomStr']);
        if(isset($data['signature'])) unset($data['signature']);
        if(empty($data)) {
            return [];
        }
        return $this->data;
    }

    public function jsonSerialize()
    {
        return $this->data;
    }

    public function __get($key)
    {
        return $this->data->$key ?? null;
    }

    public function __isset($key)
    {
        return isset($this->data->$key);
    }

    public function __toString()
    {
        return (string)$this->original;
    }
}
