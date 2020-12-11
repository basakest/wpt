<?php

namespace WptBus\Model\Sale\Discovery;

class GetDiscoveryDataNoLogin
{
    private $num = 30;
    private $ip = '';
    private $noLoginUri = '';
    private $hideCategory = [];
    private $direct = 'r';
    private $sc = '';
    private $os = '';
    private $ch = '';
    private $columns = [];
    private $page = 1;
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
    public function getPage(): int
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
     * @return array
     */
    public function getColumns(): array
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
    public function getNoLoginUri(): string
    {
        return $this->noLoginUri;
    }

    /**
     * @param string $noLoginUri
     */
    public function setNoLoginUri(string $noLoginUri)
    {
        $this->noLoginUri = $noLoginUri;
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
    public function getDirect()
    {
        return $this->direct;
    }

    /**
     * @param string $direct
     */
    public function setDirect(string $direct)
    {
        $this->direct = $direct;
    }

    /**
     * @return string
     */
    public function getSc(): string
    {
        return $this->sc;
    }

    /**
     * @param string $sc
     */
    public function setSc(string $sc)
    {
        $this->sc = $sc;
    }

    /**
     * @return string
     */
    public function getOs(): string
    {
        return $this->os;
    }

    /**
     * @param string $os
     */
    public function setOs(string $os)
    {
        $this->os = $os;
    }

    /**
     * @return string
     */
    public function getCh(): string
    {
        return $this->ch;
    }

    /**
     * @param string $ch
     */
    public function setCh(string $ch)
    {
        $this->ch = $ch;
    }
}
