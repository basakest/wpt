<?php


namespace WptUtils\Contracts\Http;


interface UrlInterface
{
    /**
     * 获取host
     *
     * @return mixed
     */
    public function getHost();

    /**
     * 获取scheme
     *
     * @return mixed
     */
    public function getScheme();

    /**
     * @return mixed
     */
    public function getQuery();

    //......
}