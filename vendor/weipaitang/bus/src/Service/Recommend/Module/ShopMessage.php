<?php

namespace WptBus\Service\Recommend\Module;

use App\ConstDir\ErrorConst;
use App\Utils\CommonUtil;
use Monolog\Logger;
use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Lib\Validator;
use WptBus\Service\Recommend\Router;

class ShopMessage extends \WptBus\Service\BaseService
{

    public function postShopMessage($params = [])
    {
        return $this->httpPost(Router::POST_SHOP_MESSAGE, $params);
    }


    public function getShopMessages($userId, $userUri, $limit=300)
    {
        $params = [];
        $params["id"] = (int)$userId;
        $params["uri"] = (string)$userUri;
        $params["limit"] = (int)$limit;
        return $this->httpPost(Router::GET_SHOP_MESSAGES, $params);
    }

}