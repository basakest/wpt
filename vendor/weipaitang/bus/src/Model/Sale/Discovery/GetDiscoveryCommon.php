<?php

namespace WptBus\Model\Sale\Discovery;

class GetDiscoveryCommon
{
    private $type = '';
    private $hideCategory = [];
    private $userUri = '';
    private $num = 30;
    private $ip = '';
    private $screen = '';
    private $showQingzhu = false;
    private $qingzhuNum = '';
    private $qingzhuType = '';
    private $columns = [];

    /**
     * @return bool
     */
    public function isShowQingzhu(): bool
    {
        return $this->showQingzhu;
    }

    /**
     * @param bool $showQingzhu
     */
    public function setShowQingzhu(bool $showQingzhu)
    {
        $this->showQingzhu = $showQingzhu;
    }

    /**
     * @return string
     */
    public function getQingzhuNum(): string
    {
        return $this->qingzhuNum;
    }

    /**
     * @param string $qingzhuNum
     */
    public function setQingzhuNum(string $qingzhuNum)
    {
        $this->qingzhuNum = $qingzhuNum;
    }

    /**
     * @return string
     */
    public function getQingzhuType(): string
    {
        return $this->qingzhuType;
    }

    /**
     * @param string $qingzhuType
     */
    public function setQingzhuType(string $qingzhuType)
    {
        $this->qingzhuType = $qingzhuType;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getHideCategory(): array
    {
        return $this->hideCategory;
    }

    /**
     * @param array $hideCategory
     */
    public function setHideCategory(array $hideCategory)
    {
        $this->hideCategory = $hideCategory;
    }

    /**
     * @return string
     */
    public function getUserUri(): string
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
     * @return int
     */
    public function getNum(): int
    {
        return $this->num;
    }

    /**
     * @param int $num
     */
    public function setNum(int $num)
    {
        $this->num = $num;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getScreen(): string
    {
        return $this->screen;
    }

    /**
     * @param string $screen
     */
    public function setScreen(string $screen)
    {
        $this->screen = $screen;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }
}
