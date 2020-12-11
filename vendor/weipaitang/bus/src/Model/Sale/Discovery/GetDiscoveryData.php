<?php

namespace WptBus\Model\Sale\Discovery;

class GetDiscoveryData
{
    private $hideCategory = [];
    private $platForm = '';
    private $page = 1;
    private $num = 30;
    private $userId = 0;
    private $userUri = '';
    private $ip = '';
    private $screen = '';
    private $columns = [];
    private $isNoviceBuyer = 0;
    private $isNewUser = 0;
    private $registerTime = 0;
    private $extraData = '';

    /**
     * @return string
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * @param string $extraData 额外透传给数据应用组的字段，格式: a=1&b=2
     */
    public function setExtraData(string $extraData)
    {
        $this->extraData = $extraData;
    }


    /**
     * @return int
     */
    public function getIsNoviceBuyer(): int
    {
        return $this->isNoviceBuyer;
    }

    /**
     * @param int $isNoviceBuyer
     */
    public function setIsNoviceBuyer(int $isNoviceBuyer)
    {
        $this->isNoviceBuyer = $isNoviceBuyer;
    }

    /**
     * @return int
     */
    public function getIsNewUser(): int
    {
        return $this->isNewUser;
    }

    /**
     * @param int $isNewUser
     */
    public function setIsNewUser(int $isNewUser)
    {
        $this->isNewUser = $isNewUser;
    }

    /**
     * @return array
     */
    public function getHideCategory()
    {
        return $this->hideCategory;
    }

    /**
     * @param array $hideCate
     */
    public function setHideCategory(array $hideCategory)
    {
        $this->hideCategory = $hideCategory;
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


    /**
     * @return string
     */
    public function getPlatForm()
    {
        return $this->platForm;
    }

    /**
     * @param string $platForm
     */
    public function setPlatForm(string $platForm)
    {
        $this->platForm = $platForm;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page)
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getNum()
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
     * @return string
     */
    public function getUserUri()
    {
        return $this->userUri;
    }

    /**
     * @param mixed $userUri
     */
    public function setUserUri(string $userUri)
    {
        $this->userUri = $userUri;
    }

    /**
     * @return string
     */
    public function getIp()
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
    public function getScreen()
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
     * @return int
     */
    public function getRegisterTime(): int
    {
        return $this->registerTime;
    }

    /**
     * @param int $registerTime
     */
    public function setRegisterTime(int $registerTime)
    {
        $this->registerTime = $registerTime;
    }
}
