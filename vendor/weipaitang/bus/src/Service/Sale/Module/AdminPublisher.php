<?php

namespace WptBus\Service\Sale\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Sale\Router;

class AdminPublisher extends BaseService
{
    /**
     * 后台发拍
     * @param array $input
     * @param $autoPayBzj
     * @param int $saleId
     * @return array
     */
    public function salePublish(array $input, $autoPayBzj, $saleId = 0)
    {
        $param = [
            'Sale' => json_encode($input, JSON_UNESCAPED_UNICODE),
            'AutoPayBzj' => $autoPayBzj,
            'SaleId' => (int)$saleId,
        ];
        $data = $this->httpPost(Router::ADMIN_PUBLISHER_SALE_PUBLISH, $param);

        return $data;
    }

    /**
     * @param $input
     * @return array
     */
    public function standardPublish(array $input)
    {
        $param = [
            'StandardGoods' => json_encode($input, JSON_UNESCAPED_UNICODE)
        ];
        $data = $this->httpPost(Router::ADMIN_PUBLISHER_STANDARD_GOODS_PUBLISH, $param);
        if (empty($data['data'])) {
            return [];
        }
        return $data['data'];
    }
}
