<?php
namespace WptBus\Service\Order\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Order\Router;

class Seller extends BaseService
{
    public function orderList(int $userinfoId, string $type, string $page, int $pageSize, array $sellerOrderColumn)
    {
        $params = [];
        $params["userinfoId"] = $userinfoId;
        $params["listType"] = $type;
        $params["score"] = $page;
        $params["limit"] = $pageSize;
        $params["saleOrderFields"] = $sellerOrderColumn;
        if ($error = $this->validate($params, [
            'userinfoId' => 'required',
            'listType' => "required",
            "limit" => "required",
        ])) {
            return $error;
        }
        $ret = $this->httpPost(Router::ORDER_SELLER_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 0);
            foreach ((array)$data as $k => $v) {
                // 将saleOrder的数据向外层拼接
                if (property_exists($v, "saleOrder")) {

                    foreach ($v->saleOrder as $k => $v2) {

                        if ($k == "status") {
                            $v->status = get_order_status_text((int)$v2);
                            continue;
                        }

                        if ($k == "unsoldReason") {
                            $v->unsoldReason = get_unsold_reason_text((int)$v2);
                            continue;
                        }

                        if ($k == "winJson") {
                            $v->win = json_decode($v2);
                            continue;
                        }
                        $v->{$k} = $v2;
                    }
                    unset($v->saleOrder);
                }
            }
            return $data;
        });
        return $ret;
    }

}