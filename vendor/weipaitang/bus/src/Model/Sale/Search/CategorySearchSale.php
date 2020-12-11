<?php

namespace WptBus\Model\Sale\Search;

class CategorySearchSale
{
    /**
     * @var string 用户id
     */
    private $userUri;

    /**
     * @var array 筛选框选择项
     */
    private $words;

    /**
     * @var int 一级分类
     */
    private $category;

    /**
     * @var int 二级分类
     */
    private $secCategory;

    /**
     * @var int 偏移量
     */
    private $offset;

    /**
     * @var int 获取数量
     */
    private $limit;

    /**
     * @var int 排序
     */
    private $sort;

    /**
     * @var string 标签id
     */
    private $tagId;

    /**
     * @var int 最小价
     */
    private $minPrice;

    /**
     * @var array 查询字段
     */
    private $fields;

    /**
     * @var int 用户id
     */
    private $userId;

    /**
     * @var int 最低加价幅度
     */
    private $minIncrease;

    /**
     * @var int 最高加价幅度
     */
    private $maxIncrease;

    /**
     * @var int 最小店铺登记
     */
    private $minShopLevel;

    /**
     * @var int 最大店铺等级
     */
    private $maxShopLevel;

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * @var int 最大价
     */
    private $maxPrice;

    /**
     * @return string
     */
    public function getUserUri()
    {
        return $this->userUri;
    }

    /**
     * @param string $userUri
     */
    public function setUserUri(string $userUri)
    {
        $this->userUri = $userUri;
    }

    /**
     * @return array
     */
    public function getWords()
    {
        return $this->words;
    }

    /**
     * @param array $words
     */
    public function setWords(array $words)
    {
        $this->words = $words;
    }

    /**
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param int $category
     */
    public function setCategory(int $category)
    {
        $this->category = $category;
    }

    /**
     * @return int
     */
    public function getSecCategory()
    {
        return $this->secCategory;
    }

    /**
     * @param int $secCategory
     */
    public function setSecCategory(int $secCategory)
    {
        $this->secCategory = $secCategory;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     */
    public function setSort(int $sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return string
     */
    public function getTagId()
    {
        return $this->tagId;
    }

    /**
     * @param string $tagId
     */
    public function setTagId(string $tagId)
    {
        $this->tagId = $tagId;
    }

    /**
     * @return int
     */
    public function getMinPrice()
    {
        return $this->minPrice;
    }

    /**
     * @param int $minPrice
     */
    public function setMinPrice(int $minPrice)
    {
        $this->minPrice = $minPrice;
    }

    /**
     * @return int
     */
    public function getMaxPrice()
    {
        return $this->maxPrice;
    }

    /**
     * @param int $maxPrice
     */
    public function setMaxPrice(int $maxPrice)
    {
        $this->maxPrice = $maxPrice;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return int
     */
    public function getMinIncrease()
    {
        return $this->minIncrease;
    }

    /**
     * @param int $minIncrease
     */
    public function setMinIncrease(int $minIncrease)
    {
        $this->minIncrease = $minIncrease;
    }

    /**
     * @return int
     */
    public function getMaxIncrease()
    {
        return $this->maxIncrease;
    }

    /**
     * @param int $maxIncrease
     */
    public function setMaxIncrease(int $maxIncrease)
    {
        $this->maxIncrease = $maxIncrease;
    }

    /**
     * @return int
     */
    public function getMinShopLevel()
    {
        return $this->minShopLevel;
    }

    /**
     * @param int $minShopLevel
     */
    public function setMinShopLevel(int $minShopLevel)
    {
        $this->minShopLevel = $minShopLevel;
    }

    /**
     * @return int
     */
    public function getMaxShopLevel()
    {
        return $this->maxShopLevel;
    }

    /**
     * @param int $maxShopLevel
     */
    public function setMaxShopLevel(int $maxShopLevel)
    {
        $this->maxShopLevel = $maxShopLevel;
    }
}
