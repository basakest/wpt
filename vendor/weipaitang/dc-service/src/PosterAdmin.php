<?php


namespace WptDataCenter;


use WptDataCenter\Handler\CurlHandler;
use WptDataCenter\Route\PostAdminRoute;

/**
 * 海报后台接口
 * Class PosterAdmin
 * @package WptDataCenter
 */
class PosterAdmin
{
    /**
     * @var $intance
     */
    protected static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }


    /**
     * @param string $name
     * @param string $description
     * @param string $config
     * @param int $retries
     * @return array
     * @throws \Throwable
     */
    public function create(string $name, string $description, string $config, $retries = 1)
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(PostAdminRoute::POSTER_CREATE, [
            "name" => $name,
            "description" => $description,
            "config" => $config
        ]);
    }

    /**
     * @param $id
     * @param $fields
     * @param int $retries
     * @return array
     * @throws \Throwable
     */
    public function update(int $id, string $fields, $retries = 1)
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(PostAdminRoute::POSTER_UPDATE, [
            "id" => $id,
            "fields" => $fields,
        ]);
    }

    /**
     * @param int $id
     * @param int $retries
     * @return array
     * @throws \Throwable
     */
    public function delete(int $id, $retries = 1)
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(PostAdminRoute::POSTER_DELTE, [
            "id" => $id,
        ]);
    }

    /**
     * @param int $id
     * @param int $retries
     * @return array
     * @throws \Throwable
     */
    public function preview(int $id, $retries = 1)
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(PostAdminRoute::POSTER_PREVIEW, [
            "id" => $id,
        ]);
    }
}
