<?php

namespace WptBus\Model\Sale\Discovery;

class GetDiscoveryGuess
{
    private $userUri = '';
    private $num = 30;
    private $ip = '';
    private $screen = '';
    private $hideCategory = [];
    private $columns = [];

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
}
