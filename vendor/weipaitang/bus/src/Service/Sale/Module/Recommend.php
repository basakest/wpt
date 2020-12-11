<?php

namespace WptBus\Service\Sale\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Sale\Router;

class Recommend extends BaseService
{
    protected function formatResult()
    {
        return function ($data) {
            if (!empty($data) && is_string($data)) {
                return json_decode($data, true);
            }
            return [];
        };
    }

    /**
     * 获取精选聚合页数据
     * @param int $start
     * @param int $end
     * @param $timeCursor
     * @return array
     */
    public function getRecommendTogether(int $start, int $end, int $timeCursor)
    {
        $data = [];
        $data['Start'] = $start;
        $data['End'] = $end;
        $data['TimeCursor'] = $timeCursor;

        $result = $this->httpPost(Router::SALE_RECOMMEND_TOGETHER, $data);
        if (!isset($result['data'])) {
            return ['code' => 500,'msg' => '网络错误'];
        }
        if (empty($result['data'])) {
            return [];
        }
        $ret = json_decode($result['data'], true);
        return $ret;
    }

    /**
     * @param string $saleUri
     * @param int $userinfoId
     * @param int $goodShopSale
     * @return array
     */
    public function setRecommendSale(string $saleUri, int $userinfoId, int $goodShopSale)
    {
        $data = [];
        $data['SaleUri'] = $saleUri;
        $data['UserInfoId'] = $userinfoId;
        $data['GoodShopSale'] = $goodShopSale;

        $result = $this->httpPost(Router::SALE_SET_RECOMMEND_SALE, $data);
        if (!is_bool($result['data'] ?? [])) {
            return ['code' => 500, 'msg' => '网络错误'];
        }

        $mapCode = [
            201071 => 2106,
            201072 => 2107,
            201073 => 2111,
            201074 => 2113
        ];

        $result['code'] = $mapCode[$result['code']] ?? $result['code'];

        return $result;
    }

    public function getRecommendSaleList(array $columns, $limit = 20, $offset = 0, $random = 0)
    {
        $result = $this->httpPost(
            Router::SALE_GET_RECOMMEND_SALE_LIST,
            [
                'columns' => $columns,
                'limit' => (int)$limit,
                'offset' => (int)$offset,
                'random' => (int)$random
            ]
        );

        if (!empty($result['data']) && is_string($result['data'])) {
            $result['data'] = json_decode($result['data'], true);
        } else {
            $result['data'] = [];
        }

        return $result;
    }

    public function getShopRecommendSaleList(int $userId, array $columns)
    {
        $result = $this->httpPost(
            Router::SALE_GET_SHOP_RECOMMEND_SALE_LIST,
            [
                'Columns' => $columns,
                'UserInfoId' => (int)$userId,
            ]
        );

        if (!empty($result['data']) && is_string($result['data'])) {
            $result['data'] = json_decode($result['data']);
        } else {
            $result['data'] = [];
        }

        return $result;
    }

    public function manageShopRecommendSaleList(
        int $userId,
        $limit,
        $offset,
        array $categories,
        string $categoryCond
    ) {
        $result = $this->httpPost(
            Router::SALE_MANAGE_SHOP_RECOMMEND_SALE_LIST,
            [
                'userId' => $userId,
                'limit' => (int)$limit,
                'offset' => (int)$offset,
                'categories' => $categories,
                'categoryCond' => $categoryCond,
            ]
        );

        if (!empty($result['data']) && is_string($result['data'])) {
            $result['data'] = json_decode($result['data']);
        } else {
            $result['data'] = [];
        }

        return $result;
    }
}
