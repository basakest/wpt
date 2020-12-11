<?php


namespace WptBus\Service\User\Module;

use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class Search extends BaseService
{
    // 玩家社区增加es搜索用户 数组中类型应为int
    public function getAppraiserUserList(
        array $appraiserLevel,
        array $userType,
        string $keyword,
        string $userinfoIds,
        int $pageNum,
        int $pageSize
    ) {
        $params = [
            "appraiserLevel" => $appraiserLevel,
            "userType" => $userType,
            "keyword" => $keyword,
            "pageNum" => $pageNum,
            "pageSize" => $pageSize,
            "userinfoIds" => $userinfoIds
        ];
        return $this->httpPost(Router::COMMUNITY_SEARCH_USER, $params);
    }

    /**
     * 根据手机号获取用户id
     * @param string $telephone
     * @return array|void
     */
    public function getUidByTel(string $telephone)
    {
        $data = ["telephone" => $telephone];
        if ($error = $this->validate($data, ["telephone" => "required|string"])) {
            return $error;
        }
        $ret = $this->httpPost(Router::CENTER_GET_LIST_BY_TELEPHONE, [
            'telephone' => $telephone,
            'fields' => ["bindId"],
        ]);
        $this->dealResultData($ret, function ($data) {
            if (empty($data)) {
                return 0;
            }
            $bindId = min(array_filter(array_column($data, 'bindId')));
            return $bindId;
        });
        return $ret;
    }

    public function getUidByUnionId(string $unionId)
    {
        $data = ["unionId" => $unionId];
        if ($error = $this->validate($data, ["unionId" => "required|string"])) {
            return $error;
        }
        $ret = $this->httpPost(Router::CENTER_GET_ONE_BY_UNION_ID, $data);
        $this->dealResultData($ret, function ($data) {
            if (empty($data)) {
                return 0;
            }
            return $data["bindId"] ?? 0;
        });
        return $ret;
    }

    /**
     * 通过企业名称搜索
     * @param $companyName
     * @return array
     * [46511478]
     */
    public function searchByCompanyName(string $companyName)
    {
        $param = [
            'companyName' => (string)$companyName,
        ];
        return $this->httpPost(Router::SEARCH_BY_COMPANY_NAME, $param);
    }

    /**
     * 根据店铺名称搜索
     * @param $shopName
     * @return array
     * [1221057,3783630,17803246]
     */
    public function searchByShopName(string $shopName)
    {
        $param = [
            'shopName' => (string)$shopName,
        ];
        return $this->httpPost(Router::SEARCH_BY_SHOP_NAME, $param);
    }

    /**
     * 通过名称(用户名/店铺名)搜索以属性（用户标签）
     * @param string $name
     * @param array $property
     * @return array
     * [46521640,5210164,52051774,17684830]
     */
    public function searchByNameWithProperty(string $name, array $property)
    {
        $param = [
            'name' => (string)$name,
            'property' => (array)$property,
        ];
        return $this->httpPost(Router::SEARCH_BY_NAME_WITH_PROPERTY, $param);
    }

    /**
     * 通过名称(用户名/店铺名)搜索
     * @param $name
     * @return array
     * [5886257,7128339,12108839,18274130]
     */
    public function searchByName(string $name)
    {
        $param = [
            'name' => (string)$name,
        ];
        return $this->httpPost(Router::SEARCH_BY_NAME, $param);
    }

    /**
     * 根据手机号和平台获取用户id
     * @param string $telephone
     * @param int $platformId
     * @return array|void
     */
    public function getUidByTelephone(string $telephone, int $platformId)
    {
        $data = [
            "telephone" => $telephone,
            "platformId" => $platformId
        ];
        if ($error = $this->validate($data, [
            "telephone" => "required|string",
            "platformId" => "required|int"
        ])) {
            return $error;
        }

        return $this->httpPost(Router::CENTER_GET_UID_TELEPHONE, [
            'telephone' => $telephone,
            'platformId' => $platformId,
        ]);
    }


    /**
     * 搜索社区用户关注的人
     * @param int $userid
     * @param string $keyword
     * @param int $offset
     * @param int $limit
     * @return array|null
     */
    public function searchUserFollow(int $userid, string $keyword, int $offset, int $limit)
    {
        if (!$userid || !$keyword) {
            return null;
        }
        return $this->httpPost(Router::COMMUNITY_SEARCH_USER_FOLLOW, [
            'userid' => (int)$userid,
            'keyword' => (string)$keyword,
            'offset' => (int)$offset,
            'limit' => (int)$limit,
        ]);
    }

}