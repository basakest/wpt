<?php
/**
 * Created by PhpStorm.
 * UserModel: luweijun
 * Date: 2018/12/12
 * Time: 11:48
 */

namespace App\Utils;

use App\ConstDir\ErrorConst;

class result
{
    /**
     * 返回数据
     * @var array
     */
    private $resultValue = [];

    /**
     * result constructor.
     * @param int $code 编号
     * @param object $msg 信息
     */
    public function __construct($code, $msg)
    {
        $this->resultValue['code'] = $code;
        $this->resultValue['msg'] = $msg;
    }

    /**
     * 是否成功
     * @return bool
     */
    public function isSuccess()
    {
        return $this->getCode() == ErrorConst::SUCCESS_CODE;
    }

    /**
     * 获取编号
     * @return mixed
     */
    public function getCode()
    {
        return $this->resultValue['code'];
    }

    /**
     * 设置编号
     * @param $code
     * @return mixed
     */
    public function setCode($code)
    {
        $this->resultValue['code'] = $code;
        return $this;
    }

    /**
     * 获取信息
     * @return mixed
     */
    public function getMsg()
    {
        return $this->resultValue['msg'];
    }

    /**
     * 设置信息
     * @param $data
     * @return mixed
     */
    public function setMsg($data)
    {
        $this->resultValue['msg'] = $data;
        return $this;
    }

    /**
     * resultValue
     * @return array
     */
    public function getResult()
    {
        return $this->resultValue;
    }

    /**
     * @param bool $isJson
     * @return array|false|string
     */
    public function responseSuccess($isJson = true)
    {
        $responseData = [
            'code' => $this->getCode(),
            'data' => $this->getMsg(),
            'msg' => 'ok',
        ];
        return !$isJson ? $responseData : json_encode($responseData);
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this->getResult());
    }
}
