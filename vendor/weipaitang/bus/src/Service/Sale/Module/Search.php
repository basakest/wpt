<?php

namespace WptBus\Service\Sale\Module;

use WptBus\Service\BaseService;
use WptBus\Model\Sale\Search\CategorySearchSale;
use WptBus\Service\Sale\Router;

class Search extends BaseService
{
    public function searchExtendedWords(string $keyword, string $scene, int $userInfoId, string $userInfoUri, int $page)
    {
        $data = [];
        $data['Keyword'] = $keyword;
        $data['Scene'] = $scene;
        $data['UserInfoId'] = $userInfoId;
        $data['UserInfoUri'] = $userInfoUri;
        $data['Page'] = $page;

        $result = $this->httpPost(Router::SEARCH_SEARCH_EXTENDED_WORDS, $data);

        $data = $result['data'] ?? [];
        if (empty($data)) {
            return ['code' => 500, 'msg' => '网络错误'];
        }

        if (!empty($data['list'])) {
            $result['data']['list'] = json_decode($data['list'], true);
        }

        return $result;
    }

    public function searchKeyword(array $data)
    {
        $result = $this->httpPost(Router::SEARCH_SEARCH_KEYWORD, $data);

        $data = $result['data'] ?? [];
        if (empty($data)) {
            return ['code' => 500, 'msg' => '网络错误'];
        }

        if (!empty($data['items'])) {
            $result['data']['items'] = json_decode($data['items'], true);
        }
        if (!empty($data['adSaleList'])) {
            $result['data']['adSaleList'] = json_decode($data['adSaleList'], true);
        }
        if (!empty($data['aboutSearch'])) {
            $result['data']['aboutSearch'] = json_decode($data['aboutSearch'], true);
        }

        return $result;
    }

    public function getTagSearchList(CategorySearchSale $dto)
    {
        $params = [];

        if ($dto->getWords()) {
            $params['words'] = json_encode($dto->getWords(), JSON_UNESCAPED_UNICODE);
        }
        if ($dto->getCategory()) {
            $params['cateId'] = $dto->getCategory();
        }
        if ($dto->getSecCategory()) {
            $params['secCate'] = $dto->getSecCategory();
        }
        if ($dto->getOffset()) {
            $params['offset'] = $dto->getOffset();
        }
        if ($dto->getLimit()) {
            $params['limit'] = $dto->getLimit();
        }
        if ($dto->getUserUri()) {
            $params['userUri'] = $dto->getUserUri();
        }
        if ($dto->getSort()) {
            $params['sort'] = $dto->getSort();
        }
        if ($dto->getTagId()) {
            $params['tagId'] = $dto->getTagId();
        }
        if ($dto->getMinPrice()) {
            $params['minPrice'] = $dto->getMinPrice();
        }
        if ($dto->getMaxPrice()) {
            $params['maxPrice'] = $dto->getMaxPrice();
        }
        if ($dto->getFields()) {
            $params['fields'] = $dto->getFields();
        }
        $minLevel = $dto->getMinShopLevel();
        if (isset($minLevel)) {
            if ($minLevel === 0) {
                $minLevel = -1;
            }
            $params['minShopLevel'] = $minLevel;
        }

        $maxShopLevel = $dto->getMaxShopLevel();
        if (isset($maxShopLevel)) {
            if ($maxShopLevel === 0) {
                $maxShopLevel = -1;
            }
            $params['maxShopLevel'] = $maxShopLevel;
        }

        $minIncrease = $dto->getMinIncrease();
        if (isset($minIncrease)) {
            if ($minIncrease === 0) {
                $minIncrease = -1;
            }
            $params['minIncrease'] = $minIncrease;
        }

        $maxIncrease = $dto->getMaxIncrease();
        if (isset($maxIncrease)) {
            if ($maxIncrease === 0) {
                $maxIncrease = -1;
            }
            $params['maxIncrease'] = $maxIncrease;
        }

        $result = $this->httpPost(Router::SEARCH_GET_TAG_SEARCH_LIST, $params);

        if (!empty($result['data']) && is_string($result['data'])) {
            $result['data'] = json_decode($result['data'], true);
        }

        return $result;
    }

    public function getCategorySearchList(CategorySearchSale $dto)
    {
        $params = [];

        if ($dto->getUserUri()) {
            $params['userUri'] = $dto->getUserUri();
        }
        if ($dto->getCategory()) {
            $params['category'] = $dto->getCategory();
        }
        if ($dto->getSecCategory()) {
            $params['secCategory'] = $dto->getSecCategory();
        }
        if ($dto->getSort()) {
            $params['sort'] = $dto->getSort();
        }
        if ($dto->getOffset()) {
            $params['start'] = $dto->getOffset();
        }
        if ($dto->getLimit()) {
            $params['end'] = $dto->getLimit();
        }
        if ($dto->getTagId()) {
            $params['tagId'] = $dto->getTagId();
        }
        if ($dto->getUserId()) {
            $params['userId'] = $dto->getUserId();
        }

        $result = $this->httpPost(Router::SEARCH_GET_CATEGORY_SEARCH_LIST, $params);

        if (!empty($result['data']) && is_string($result['data'])) {
            $result['data'] = json_decode($result['data'], true);
        }

        return $result;
    }
}
