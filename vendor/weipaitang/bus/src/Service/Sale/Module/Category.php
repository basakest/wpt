<?php

namespace WptBus\Service\Sale\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Sale\Router;

class Category extends BaseService
{
    protected  function formatResult()
    {
        return function ($data) {
            if (!empty($data) && is_string($data)) {
                return json_decode($data, true);
            }
            return [];
        };
    }

    /**
     * 获取类目聚合结构
     */
    public function getCategoryTree()
    {
        $data = [];

        $result = $this->httpPost(Router::GET_CATEGORY_TREE, $data);
        $this->dealResultData($result, $this->formatResult());
        return $result;
    }

    /**
     * 获取类目的子类目
     * @param int $categoryId
     */
    public function getChildCategory(int $categoryId)
    {
        $data = [
            "categoryId" => $categoryId
        ];

        $result = $this->httpPost(Router::GET_CHILD_CATEGORY, $data);
        $this->dealResultData($result, $this->formatResult());
        return $result;
    }

    /**
     * 获取一级类目
     */
    public function getRootCategory()
    {
        return $this->getChildCategory(-1);
    }
}

