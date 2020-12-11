<?php


namespace WptDataCenter;

use WptDataCenter\Handler\CurlHandler;
use WptDataCenter\Route\Route;

class DataCenterAdmin
{

    /**
     * @var $intance
     */
    protected static $instance;


    /**
     * @return DataCenterAdmin
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function adminGetBusinessNameList(string $business, $uniqueId, $page, $pageSize, int $retries = 1)
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(Route::ADMIN_GET_BUSINESS_NAME_LIST, [
            "business" => $business,
            "uniqueId" => strval($uniqueId),
            "page" => intval($page),
            "pageSize" => intval($pageSize)
        ]);
    }

    public function adminEditBusinessNameList(int $id, string $business, $businessDesc, int $retries = 1)
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(Route::ADMIN_EDIT_BUSINESS_NAME_LIST, [
            "id" => $id,
            "business" => $business,
            "businessDesc" => $businessDesc
        ]);
    }

    public function adminSyncBusinessNameList(string $business, int $retries = 1)
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(Route::ADMIN_SYNC_BUSINESS_NAME_LIST, [
            "business" => $business
        ]);
    }

    public function adminGetUserListByBusiness(string $business, string $userId, int $page, int $pageSize, $retries = 1)
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(Route::ADMIN_GET_USER_LIST_BY_BUSINESS, [
            "business" => $business,
            "userId" => strval($userId),
            "page" => intval($page),
            "pageSize" => intval($pageSize)
        ]);
    }

    public function adminDeleteUserFromBusinessList(string $business, string $userSign, int $retries = 1)
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(Route::ADMIN_DELETE_USER_FROM_BUSINESS_LIST, [
            "business" => $business,
            "userId" => strval($userSign)
        ]);
    }

    public function adminAppendBusinessList(string $business, array $content, int $expireTime = 0, string $reason = "", int $retries = 1)
    {
        $uids = array_map(function ($item) {
            return strval($item);
        }, $content);
        return CurlHandler::getInstance()->setRetries($retries)->go(Route::ADMIN_APPEND_BUSINESS_LIST, [
            "business" => $business,
            "content" => $uids,
            "reason" => $reason,
            "expireTime" => $expireTime
        ]);
    }

    public function adminBigDataMappingTableList(int $page, int $pageSize, $retries = 1)
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(Route::ADMIN_BIG_DATA_MAP_TABLE_LIST, [
            "page" => $page,
            "pageSize" => $pageSize
        ]);
    }

    public function adminEditBigDataMappingTable(int $id, array $data, $retries = 1)
    {
        $data["id"] = $id;
        return CurlHandler::getInstance()->setRetries($retries)->go(Route::ADMIN_EDIT_BIG_DATA_MAP_TABLE, $data);
    }

    public function adminDeleteBigDataMappingTable(int $id, $retries = 1)
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(Route::ADMIN_DELETE_BIG_DATA_MAP_TABLE, [
            "id" => $id
        ]);
    }

    public function adminBigDataMappingFieldsByTable(string $table, $retries = 1)
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(Route::ADMIN_BIG_DATA_MAP_FIELDS_BY_TABLE, [
            "table" => $table
        ]);
    }

    public function adminEditBigDataMappingFields(int $id, array $data, $retries = 1)
    {
        $data["id"] = $id;
        return CurlHandler::getInstance()->setRetries($retries)->go(Route::ADMIN_EDIT_BIG_DATA_MAP_FIELDS, $data);
    }

    public function adminDeleteBigDataMappingFields(int $id, $retries = 1)
    {
        return CurlHandler::getInstance()->setRetries($retries)->go(Route::ADMIN_DELETE_BIG_DATA_MAP_FIELDS, [
            "id" => $id
        ]);
    }

}





