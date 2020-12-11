<?php

namespace WptBus\Service\Order\Module;

use App\ConstDir\ErrorConst;
use App\Utils\CommonUtil;
use Monolog\Logger;
use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Lib\Validator;
use WptBus\Service\Order\Router;

class BuyerRate extends \WptBus\Service\BaseService
{

    public function getBuyerRateInfo($userId, $saleUri)
    {
        $params = [];
        $params["userId"] = (int)$userId;
        $params["saleUri"] = (string)$saleUri;
        $ret = $this->httpPost(Router::GET_BUYER_RATE_INFO, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    public function addBuyerRate(array $params = [])
    {
        $ret = $this->httpPost(Router::ADD_BUYER_RATE_INFO, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    public function appendBuyerRate(array $params = [])
    {
        $ret = $this->httpPost(Router::APPEND_BUYER_RATE_INFO, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    public function modifyBuyerRate(array $params = [])
    {
        $ret = $this->httpPost(Router::MODIFY_BUYER_RATE_INFO, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    public function deleteBuyerRate(array $params = [])
    {
        $ret = $this->httpPost(Router::DELETE_BUYER_RATE_INFO, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    public function setRateInvalid(array $params = [])
    {
        $ret = $this->httpPost(Router::SET_RATE_INVALID, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    public function clearTimeEndSaleRateTags()
    {
        $this->httpPost(Router::CLEAR_TIME_END_SALE_RATE_TAGS);
    }

}