<?php


namespace WptBus\Service\Sale\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Sale\Router;

class Brand extends BaseService
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
     * 批量获取品牌列表
     * @param array $ids
     * @return array
     */
    public function batchGetBrand(array $ids)
    {
        $ids = array_map(function ($id) {
            return intval($id);
        }, $ids);

        $data = [
            'ids' => $ids,
        ];

        $result = $this->httpPost(Router::BATCH_GET_BRAND, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 创建品牌
     * @param array $params
     * @return int
     */
    public function createBrand(array $params)
    {
        $createData = [];

        if (!empty($params['brandName'])) {
            $createData['brandName'] = (string) $params['brandName'];
        }

        if (!empty($params['brandNameEn'])) {
            $createData['brandNameEn'] = (string) $params['brandNameEn'];
        }

        if (!empty($params['shortName'])) {
            $createData['shortName'] = (string) $params['shortName'];
        }

        if (!empty($params['country'])) {
            $createData['country'] = (int) $params['country'];
        }

        if (!empty($params['areaType'])) {
            $createData['areaType'] = (int) $params['areaType'];
        }

        if (!empty($params['logo'])) {
            $createData['logo'] = (string) $params['logo'];
        }

        if (!empty($params['isValid'])) {
            $createData['isValid'] = (int) $params['isValid'];
        }

        if (!empty($params['description'])) {
            $createData['description'] = (string) $params['description'];
        }

        return $this->httpPost(Router::CREATE_BRAND, $createData);
    }

    /**
     * 更新品牌
     * @param int $id
     * @param array $params
     * @return int
     */
    public function updateBrand(int $id, array $params)
    {
        $data = [
            'id' => $id,
            'data' => json_encode($params),
        ];

        return $this->httpPost(Router::UPDATE_BRAND, $data);
    }

    /**
     * 搜索品牌
     * @param string $keyword
     * @param int $areaType
     * @param int $country
     * @param int $isValid
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getBrandList(
        string $keyword = '',
        int $areaType = -1,
        int $country = -1,
        int $isValid = -1,
        string $order = 'createTime desc',
        int $limit = 20,
        int $offset = 0
    ) {
        $where = [];

        if ($keyword != "") {
            if (preg_match('/^\d*$/', $keyword)) {
                // 只搜id
                $id = intval($keyword);
                $where['id'] = $id;
            } else {
                // 关键字模糊搜索
                $keyword = addslashes($keyword);
                $sql = "(`brandName` like '%$keyword%' or `brandNameEn` like '%$keyword%' or `shortName` like '%$keyword%')";
                $where[$sql] = null;
            }
        }

        if ($areaType != -1) {
            $where['areaType'] = $areaType;
        }

        if ($country != -1) {
            $where['country'] = $country;
        }

        if ($isValid != -1) {
            $where['isValid'] = $isValid;
        }

        $data = [
            "where" => $where ? json_encode($where, JSON_UNESCAPED_UNICODE) : "{}",
            "order" => $order,
            "limit" => $limit,
            "offset" => $offset,
        ];

        $result = $this->httpPost(Router::GET_BRAND_LIST, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    public function getBrandListRaw(
        array $where,
        string $order = 'createTime desc',
        int $limit = 20,
        int $offset = 0
    ) {
        $data = [
            "where" => json_encode($where, JSON_UNESCAPED_UNICODE),
            "order" => $order,
            "limit" => $limit,
            "offset" => $offset,
        ];

        $result = $this->httpPost(Router::GET_BRAND_LIST, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 品牌数量
     * @param string $keyword
     * @param int $areaType
     * @param int $country
     * @param int $isValid
     * @return int
     */
    public function getBrandCount(
        string $keyword = '',
        int $areaType = -1,
        int $country = -1,
        int $isValid = -1
    ) {
        $where = [];

        if ($keyword != "") {
            if (preg_match('/\d+/', $keyword)) {
                // 只搜id
                $id = intval($keyword);
                $where['id'] = $id;
            } else {
                // 关键字模糊搜索
                $keyword = addslashes($keyword);
                $sql = "`brandName` like '%$keyword%' or `brandNameEn` like '%$keyword%' or `shortName` like '%$keyword%'";
                $where[$sql] = null;
            }
        }

        if ($areaType != -1) {
            $where['areaType'] = $areaType;
        }

        if ($country != -1) {
            $where['country'] = $country;
        }

        if ($isValid != -1) {
            $where['isValid'] = $isValid;
        }

        $data = [
            "where" => $where ? json_encode($where, JSON_UNESCAPED_UNICODE) : "{}",
        ];

        return $this->httpPost(Router::GET_BRAND_COUNT, $data);
    }

    public function getBrandCategoryRelationList(
        int $categoryId = -1,
        string $order = 'displayOrder asc',
        int $limit = 20,
        int $offset = 0
    ) {
        $where = [];

        if ($categoryId != -1) {
            $where['categoryId'] = $categoryId;
        }

        $data = [
            "where" => $where ? json_encode($where, JSON_UNESCAPED_UNICODE) : '{}',
            "order" => $order,
            "limit" => $limit,
            "offset" => $offset,
        ];

        $result = $this->httpPost(Router::GET_BRAND_CATEGORY_RELATION_LIST, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    public function createBrandCategoryRelation(int $categoryId, array $brandIds)
    {
        $brandIds = array_map(function ($brandId) {
            return intval($brandId);
        }, $brandIds);

        $data = [
            'categoryId' => $categoryId,
            'brandIds' => $brandIds,
        ];

        return $this->httpPost(Router::CREATE_BRAND_CATEGORY_RELATION, $data);
    }

    public function updateBrandCategoryRelation(int $id, array $data)
    {
        $data = [
            'id' => $id,
            'data' => json_encode($data),
        ];

        return $this->httpPost(Router::UPDATE_BRAND_CATEGORY_RELATION, $data);
    }

    public function deleteBrandCategoryRelation(int $id)
    {
        $data = [
            'id' => $id,
        ];

        return $this->httpPost(Router::DELETE_BRAND_CATEGORY_RELATION, $data);
    }

    public function getBrandCategoryRelationCount(int $categoryId)
    {
        $where = [
            'categoryId' => $categoryId,
        ];

        return $this->getBrandCategoryRelationCountRaw($where);
    }

    public function getBrandCategoryRelationCountRaw($where)
    {
        $data = [
            'where' => json_encode($where),
        ];

        return $this->httpPost(Router::GET_BRAND_CATEGORY_RELATION_COUNT, $data);
    }
}
