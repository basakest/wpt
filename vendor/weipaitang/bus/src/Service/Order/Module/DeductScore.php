<?php

namespace WptBus\Service\Order\Module;

use App\ConstDir\ErrorConst;
use App\Utils\CommonUtil;
use Monolog\Logger;
use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Lib\Validator;
use WptBus\Service\Order\Router;

class DeductScore extends \WptBus\Service\BaseService
{



    /**
     * 添加订单扣分
     * @param int $orderId
     * @param int $deductScore
     * @param string $deductScoreReason
     * @param int $lastScore
     * @return array
     */
    public function addDeductScore(int $orderId, int $deductScore, string $deductScoreReason, int $lastScore)
    {
        $params["orderId"] = (int)$orderId;
        $params["deductScore"] = (int)$deductScore;
        $params["deductScoreReason"] = trim((string)$deductScoreReason);
        $params["lastScore"] = (int)$lastScore;
        $this->validate($params, [
            'orderId' => 'required',
            'deductScore' => 'required',
            'deductScoreReason' => 'required',
        ]);
        $ret = $this->httpPost(Router::ADD_DEDUCT_SCORE, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * 获取订单扣分
     * @param int $orderId
     * @return array
     */
    public function getDeductScoreList(int $orderId)
    {
        $params["orderId"] = (int)$orderId;
        $this->validate($params, [
            'orderId' => 'required'
        ]);
        $ret = $this->httpPost(Router::GET_DEDUCT_SCORE_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }
}