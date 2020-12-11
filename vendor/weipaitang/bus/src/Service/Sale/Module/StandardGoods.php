<?php

namespace WptBus\Service\Sale\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Sale\Router;

class StandardGoods extends BaseService
{
    /**
     * @param $id
     * @param $fields
     * @return array
     */
    public function getById($id, array $fields)
    {
        $data = [
            'Id' => (int)$id,
            'Columns' => $fields,
        ];

        $result = $this->httpPost(Router::GET_STANDARD_GOODS_BY_ID, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * @param $ids
     * @param $fields
     * @return array
     */
    public function multiGetByIds(array $ids, array $fields)
    {
        $data = [
            'Ids' => $ids,
            'Columns' => $fields,
        ];

        $result = $this->httpPost(Router::GET_STANDARD_GOODS_MULTI_GET_BY_IDS, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * @param $id
     * @param $updateData
     * @return array
     */
    public function update($id, array $updateData)
    {
        $data = [
            'Id' => (int)$id,
            'Data' => json_encode($updateData),
        ];

        return $this->httpPost(Router::STANDARD_GOODS_UPDATE, $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function delete($id)
    {
        $data = [
            'Id' => $id,
        ];

        return $this->httpPost(Router::STANDARD_GOODS_DELETE, $data);
    }

    /**
     * @param $id
     * @param $val
     * @return array
     */
    public function updateStock($id, $val)
    {
        $data = [
            'Id' => (int)$id,
            'Value' => (int)$val,
        ];

        return $this->httpPost(Router::STANDARD_GOODS_UPDATE_STOCK, $data);
    }

    /**
     * @param $where
     * @return array
     */
    public function getCount($where)
    {
        $data = [
            'where' => $where,
        ];

        return $this->httpPost(Router::GET_STANDARD_GOODS_COUNT, $data);
    }

    /**
     * @param $uri
     * @return array
     */
    public function uriToId($uri)
    {
        $data = [
            'uri' => $uri,
        ];

        return $this->httpPost(Router::STANDARD_GOODS_URI_2_ID, $data);
    }

    /**
     * @param array $param
     * @return array|mixed
     */
    public function insertDraft(array $param)
    {
        $data = [];
        if (!empty($param['category'])) {
            $data['category'] = (int)$param['category'];
        }
        if (!empty($param['secCategory'])) {
            $data['secCategory'] = (int)$param['secCategory'];
        }
        if (!empty($param['secCategoryTemplate'])) {
            $data['secCategoryTemplate'] = (string)$param['secCategoryTemplate'];
        }
        if (!empty($param['imgList'])) {
            $data['imgList'] = $param['imgList'];
        }
        if (!empty($param['video'])) {
            $data['video'] = (string)$param['video'];
        }
        if (!empty($param['goodsId'])) {
            $data['draftId'] = (int)$param['goodsId'];
        }
        if (!empty($param['content'])) {
            $data['content'] = (string)$param['content'];
        }
        if (!empty($param['title'])) {
            $data['title'] = (string)$param['title'];
        }
        if (!empty($param['saleType'])) {
            $data['saleType'] = (int)$param['saleType'];
        }
        if (!empty($param['depotPrId'])) {
            $data['depotPrId'] = (int)$param['depotPrId'];
        }
        if (!empty($param['depotUserId'])) {
            $data['depotUserId'] = (int)$param['depotUserId'];
        }
        if (!empty($param['appSource'])) {
            $data['appSource'] = (string)$param['appSource'];
        }
        if (!empty($param['identCertImgList'])) {
            $data['identCertImgList'] = $param['identCertImgList'];
        }
        if (!empty($param['liquorYear'])) {
            $data['liquorYear'] = $param['liquorYear'];
        }
        if (!empty($param['liquorAuthCertImgList'])) {
            $data['liquorAuthCertImgList'] = $param['liquorAuthCertImgList'];
        }

        return $this->httpPost(Router::STANDARD_INSERT_DRAFT, $data, [
            'loginuserid' => intval($param['loginUserId'] ?? 0),
        ]);
    }

    /**
     * 一口价 获取发拍设置
     * @param array $param
     * @return array|mixed
     */
    public function getSale(array $param)
    {
        $data = [];
        if (!empty($param['standardGoodsId'])) {
            $data['standardGoodsId'] = (int)$param['standardGoodsId'];
        }
        $result = $this->httpPost(Router::STANDARD_GET_SALE, $data, [
            'loginuserid' => intval($param['loginUserId'] ?? 0),
        ]);
        $result["data"] = $this->format($result["data"] ?? []);

        return $result;
    }

    /**
     * @param array $param
     * @return array|mixed
     */
    public function insertSale(array $param)
    {
        $data = [];
        if (!empty($param['goodsId'])) {
            $data['draftId'] = (int)$param['goodsId'];
        }
        if (!empty($param['category'])) {
            $data['category'] = (int)$param['category'];
        }
        if (!empty($param['secCategory'])) {
            $data['secCategory'] = (int)$param['secCategory'];
        }
        if (!empty($param['stock'])) {
            $data['stock'] = (int)$param['stock'];
        }
        if (!empty($param['enableReturn'])) {
            $data['enableReturn'] = (int)$param['enableReturn'];
        }
        if (!empty($param['expressFee'])) {
            $data['expressFee'] = (string)$param['expressFee'];
        }
        if (!empty($param['proportions'])) {
            $data['proportions'] = (int)$param['proportions'];
        }
        if (!empty($param['price'])) {
            $data['price'] = (int)$param['price'];
        }
        if (!empty($param['isBuyLimit'])) {
            $data['isBuyLimit'] = (int)$param['isBuyLimit'];
        }
        if (!empty($param['buyLimit'])) {
            $data['buyLimit'] = (int)$param['buyLimit'];
        }
        if (!empty($param['sellTime'])) {
            $data['sellTime'] = (int)$param['sellTime'];
        }

        return $this->httpPost(Router::STANDARD_INSERT_SALE, $data, [
            'loginuserid' => intval($param['loginUserId'] ?? 0),
        ]);
    }

    /**
     * @param array $param
     * @return array|mixed
     */
    public function getDraft(array $param)
    {
        $data = [];
        if (!empty($param['standardGoodsId'])) {
            $data['standardGoodsId'] = (int)$param['standardGoodsId'];
        }
        if (!empty($param['depotPrId'])) {
            $data['depotPrId'] = (int)$param['depotPrId'];
        }
        if (!empty($param['depotUserId'])) {
            $data['depotUserId'] = (int)$param['depotUserId'];
        }
        $result = $this->httpPost(Router::STANDARD_GET_DRAFT, $data, [
            'loginuserid' => intval($param['loginUserId'] ?? 0),
        ]);

        $result["data"] = $this->format($result["data"] ?? []);
        return $result;
    }

    /**
     * @param $input
     * @return array
     */
    public function UnionStandardPublish(array $input)
    {
        $param = [
            'StandardGoods' => json_encode($input, JSON_UNESCAPED_UNICODE)
        ];
        $data = $this->httpPost(Router::UNION_STANDARD_GOODS_PUBLISH, $param);
        return $data;
    }

    /**
     * 将proto中json字符串的字段解析出来
     * @param array $data
     * @return array
     */
    protected function format(array $data)
    {
        if (!empty($data["categoryList"]) && is_string($data["categoryList"])) {
            $data["categoryList"] = json_decode($data['categoryList'], true);
        }
        if (!empty($data["templateList"]) && is_string($data["templateList"])) {
            $data["templateList"] = json_decode($data['templateList'], true);
        }
        if (!empty($data["withChainCodes"]) && is_string($data["withChainCodes"])) {
            $data["withChainCodes"] = json_decode($data['withChainCodes']);
        }
        return $data;
    }

    protected function formatResult()
    {
        return function ($data) {
            if (!empty($data) && is_string($data)) {
                return json_decode($data, true);
            }
            return [];
        };
    }
}
