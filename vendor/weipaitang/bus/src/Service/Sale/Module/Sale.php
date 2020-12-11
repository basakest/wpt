<?php


namespace WptBus\Service\Sale\Module;

use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Service\BaseService;
use WptBus\Service\Sale\DTO\SealPublish\SealPublish;
use WptBus\Service\Sale\Router;

class Sale extends BaseService
{
    /**
     * 拍品详情页聚合接口
     * @param int $userinfoId
     * @param int $saleId
     * @param int $likeNum
     * @param array $cols
     * @return array
     */
    public function getSaleInfoAggregation(int $userinfoId, int $saleId, int $likeNum, array $cols)
    {
        $data = [
            'SaleId' => $saleId,
            'UserInfoId' => $userinfoId,
            "LikeNum" => $likeNum,
            "Cols" => $cols,
        ];

        $result = $this->httpPost(Router::GET_SALE_INFO_AGGREGATION, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    protected function formatResult()
    {
        return function ($data) {
            if (!empty($data) && is_string($data)) {
                return json_decode($data, true);
            }
            return [];
        };
    }

    public function arrayToObject($arr)
    {
        if (is_array($arr)) {
            return json_decode(json_encode($arr));
        } else {
            return $arr;
        }
    }

    /**
     * @param $insertData
     * @return array
     */
    public function insertSale(array $insertData)
    {
        $mustFieldList = [
            "openTime",
            "createTime",
            "endTime",
            "status",
            "profileJson",
            "uri",
        ];

        foreach ($mustFieldList as $mustField) {
            if (!isset($insertData[$mustField])) {
                return Response::byBus(Error::INVALID_ARGUMENT);
            }
        }

        $data = [
            'Sale' => json_encode($insertData),
        ];

        return $this->httpPost(Router::INSERT_SALE, $data);
    }

    /**
     * @param $id
     * @param $updateData
     * @return array
     */
    public function updateSale($id, array $updateData)
    {
        $data = [
            'saleId' => intval($id),
            'data' => json_encode($updateData),
        ];

        return $this->httpPost(Router::UPDATE_SALE, $data);
    }

    /**
     * @param $saleIdUri
     * @param $userInfoId
     * @param array $columns
     * @return array
     */
    public function getSaleWithBid($saleIdUri, $userInfoId, $columns = [])
    {
        $data = [
            'saleIdUri' => $saleIdUri,
            'userinfoId' => $userInfoId,
            'columns' => $columns,
        ];

        $result = $this->httpPost(Router::GET_SALE_WITH_BID, $data);
        $this->dealResultData($result, $this->formatResult());
        return $result;
    }

    // 逛逛小场景
    public function getDiscoverySmallScene(
        array $columns,
        int $rows,
        int $page,
        string $sceneType,
        array $filterCate,
        int $userinfoId,
        string $userinfoUri,
        int $needAds
    ) {
        $data = [];
        $data['Type'] = $sceneType;
        $data['Columns'] = $columns;
        $data['HideCategory'] = $filterCate;
        $data['Num'] = $rows;
        $data['Page'] = $page;
        $data['UserID'] = $userinfoId;
        $data['UserUri'] = $userinfoUri;
        $data['Scene'] = 'small_scene';
        $data['NeedAds'] = $needAds;

        $result = $this->httpPost(Router::GET_DISCOVERY_SALE_LIST, $data);
        $this->dealResultData($result, $this->formatResult());
        return $result;
    }

    /**
     * 获取拍品子表列表
     * @param string $type
     * @param int $userinfoId
     * @param array $fields
     * @param string $score
     * @param int $limit
     * @return array
     */
    public function getSaleManageList($type, $userinfoId, $fields = [], $score = '', $limit = 10)
    {
        $data = [];
        $data['Type'] = (string) $type;
        $data['UserInfoId'] = (int) $userinfoId;
        $data['Columns'] = $fields;
        $data['Score'] = $score;
        $data['Limit'] = $limit;

        $result = $this->httpPost(Router::GET_SALE_MANAGE_LIST, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 拍品下架接口
     * @param string $saleIdUri
     * @param int $goodsId
     * @return array
     */
    public function toSaleDel($saleIdUri, $goodsId)
    {
        $data = [
            'SaleIdUri' => strval($saleIdUri),
            'DraftId' => $goodsId,
        ];
        $result = $this->httpPost(Router::TO_SALE_DEL, $data);
        return $result;
    }

    /**
     * 批量获取拍品列表
     * 兼容老的sale-go接口,返回值为id => saleData
     * @param array $idUris
     * @param array $fields
     * @return array
     */
    public function multiGetSale(array $idUris, array $fields)
    {

        if (empty($idUris)) {
            return [];
        }

        $idUris = array_map(function ($idUri) {
            return (string) $idUri;
        }, $idUris);

        $idUris = array_values($idUris);

        $data = [];
        $data['IdUris'] = $idUris;
        $data['Columns'] = $fields;

        $result = $this->httpPost(Router::MULTI_GET_SALE, $data);

        if (!isset($result['data'])) {
            return [];
        }

        $res = $result['data'];
        if (empty($res)) {
            return [];
        }

        $res = json_decode($res, true);

        $data = $this->toSaleIdKeyArray($res, $idUris);

        $data = $this->sortByIds($idUris, $data);

        return $data;
    }

    public function toSaleIdKeyArray($arr, $idUris)
    {
        $resKeyById = [];
        foreach ($arr as $v) {
            if (in_array($v['id'], $idUris)) {
                $resKeyById[$v['id']] = $v;
            } elseif (in_array($v['uri'], $idUris)) {
                $resKeyById[$v['uri']] = $v;
            }
        }

        $data = [];
        foreach ($idUris as $id) {
            if (isset($resKeyById[$id])) {
                $data[$id] = $resKeyById[$id];
            } else {
                $data[$id] = null;
            }
        }

        return $data;
    }

    public function sortByIds(array $ids, $result)
    {
        if (empty($ids) || empty($result) || !is_array($result)) {
            return $result;
        }
        $fIds = array_flip($ids);
        uksort($result, function ($a, $b) use ($fIds) {
            if (isset($fIds[$a]) && isset($fIds[$b])) {
                return $fIds[$a] <=> $fIds[$b];
            }
            return 0;
        });
        return $result;
    }

    /**
     * 拍品详情页
     * @param array $needData 需要的数据 "sale", "shop", "like", "bid", "order"
     * @param int $userinfoId 当前登录用户id
     * @param string $saleUri 拍品uri
     * @return array
     */
    public function getSaleDetail(array $needData, int $userinfoId, string $saleUri)
    {
        $data = [
            'NeedData' => $needData,
            'UserInfoId' => $userinfoId,
            'SaleUri' => $saleUri
        ];
        $result = $this->httpPost(Router::GET_SALE_DETAIL, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * @param $id
     * @param $fields
     * @return array
     */
    public function getSaleInfoById($id, $fields)
    {
        $res = $this->multiGetSaleInfoByIds([$id], $fields);

        if ($res['code'] == 0 && is_array($res['data']) && sizeof($res['data']) > 0) {
            $res['data'] = $res['data'][0];
        }

        return $res;
    }

    /**
     * @param $uri
     * @param $fields
     * @return array
     */
    public function getSaleInfoByUri($uri, $fields)
    {
        $res = $this->multiGetSaleInfoByUris([$uri], $fields);

        if ($res['code'] == 0 && is_array($res['data']) && sizeof($res['data']) > 0) {
            $res['data'] = $res['data'][0];
        }

        return $res;
    }

    /**
     * @param $ids
     * @param $fields
     */
    public function multiGetSaleInfoByIds($ids, $fields)
    {
        $data = [
            'saleIds' => $ids,
            'columns' => $fields,
        ];

        $result = $this->httpPost(Router::MULTI_GET_SALE_INFO, $data);
        $this->dealResultData($result, $this->formatSaleInfo());

        return $result;
    }

    /**
     * @param $uris
     * @param $fields
     */
    public function multiGetSaleInfoByUris($uris, $fields)
    {
        $data = [
            'saleUris' => $uris,
            'columns' => $fields,
        ];

        $result = $this->httpPost(Router::MULTI_GET_SALE_INFO, $data);
        $this->dealResultData($result, $this->formatSaleInfo());

        return $result;
    }

    /**
     * @return \Closure
     */
    protected function formatSaleInfo()
    {
        return function ($data) {
            $data = $this->formatResult()($data);
            if (is_array($data) && sizeof($data) > 0) {
                foreach ($data as $k => $v) {
                    //处理cate
                    if (!empty($data[$k]['cate'])) {
                        $data[$k]['cate'] = json_decode($v['cate'], true);
                    }

                    //处理fee
                    if (!empty($data[$k]['fee'])) {
                        $data[$k]['fee'] = json_decode($v['fee'], true);
                    }

                    //处理identify
                    if (!empty($data[$k]['identify'])) {
                        $data[$k]['identify'] = json_decode($v['identify'], true);
                    }

                    //处理depot
                    if (!empty($data[$k]['depot'])) {
                        $data[$k]['depot'] = json_decode($v['depot'], true);
                    }

                    //处理activity
                    if (!empty($data[$k]['activity'])) {
                        $data[$k]['activity'] = json_decode($v['activity'], true);
                    }

                    //处理share
                    if (!empty($data[$k]['share'])) {
                        $data[$k]['share'] = json_decode($v['share'], true);
                    }

                    //处理standardGoods
                    if (!empty($data[$k]['standardGoods'])) {
                        $data[$k]['standardGoods'] = json_decode($v['standardGoods'], true);
                    }

                    //处理preSale
                    if (!empty($data[$k]['preSale'])) {
                        $data[$k]['preSale'] = json_decode($v['preSale'], true);
                    }

                    //处理bizFlags
                    if (!empty($data[$k]['bizFlags'])) {
                        $data[$k]['bizFlags'] = json_decode($v['bizFlags'], true);
                    }

                    //处理customProps
                    if (!empty($data[$k]['customProps'])) {
                        $data[$k]['customProps'] = json_decode($v['customProps'], true);
                    }

                    //处理systemBzjJson
                    if (!empty($data[$k]['systemBzjJson'])) {
                        $data[$k]['systemBzjJson'] = json_decode($v['systemBzjJson'], true);
                    }

                    // 处理priceJson
                    if (!empty($data[$k]['priceJson'])) {
                        $data[$k]['priceJson'] = json_decode($v['priceJson'], true);
                    }

                    // 处理secCategoryTemplate
                    if (!empty($data[$k]['secCategoryTemplate'])) {
                        $data[$k]['secCategoryTemplate'] = json_decode($v['secCategoryTemplate'], true);
                    }
                }
            }

            return $data;
        };
    }

    // 暗拍上拍接口
    public function sealSalePublish(array $saleInfo, SealPublish $profile, int $endTime)
    {
        $saleInfo['profileJson'] = json_encode($profile, JSON_UNESCAPED_UNICODE);
        $data = [
            'Scene' => $profile->getMScene(),
            'EndTime' => $endTime,
            'SaleInfo' => json_encode($saleInfo)
        ];

        $result = $this->httpPost(Router::SEAL_SALE_PUBLISH, $data);
        $this->dealResultData($result, function ($data) {
            return $data;
        });

        return $result;
    }

    // 更新拍品可鉴定标签
    public function toUpdateEnableIdentTag(int $saleId, int $enableIdent)
    {
        $data = [
            'SaleID' => $saleId,
            'EnableIdent' => $enableIdent
        ];

        return $this->httpPost(Router::TO_UPDATE_ENABLE_IDENT_TAG, $data);
    }

    // 临时接口，后期删除
    public function insertSubSale(int $saleId, array $data)
    {
        $data["id"] = $saleId;

        $insertData = [
            'Sale' => json_encode($data),
        ];

        return $this->httpPost(Router::INSERT_SUB_SALE, $insertData);
    }

    // 临时接口，后期删除
    public function updateSubSale(string $idUri, array $data)
    {
        $updateData = [
            'idUri' => $idUri,
            'data' => json_encode($data),
        ];

        return $this->httpPost(Router::UPDATE_SUB_SALE, $updateData);
    }

    public function getPublishCount(int $userInfoId)
    {
        $data = [
            'UserInfoId' => $userInfoId,
        ];

        return $this->httpPost(Router::GET_PUBLISH_COUNT, $data);
    }

    /**
     * 通过拍品id获取运费模板信息
     * @param int $saleId 拍品/标品id
     * @param int $saleType 拍品类型,1: 拍品, 2: 标品, 3: 直播间一口价
     * @param array
     */
    public function getSaleExpressFeeTemplate($saleId, $saleType = 1)
    {
        $result = $this->batchGetSaleExpressFeeTemplate([intval($saleId)], $saleType);

        if ($result['code'] == 0 && is_array($result['data']) && count($result['data']) > 0) {
            if (isset($result['data'][$saleId])) {
                $result['data'] = $result['data'][$saleId];
            }
        }

        return $result;
    }

    /**
     * 通过拍品id获取运费模板信息
     * @param array $saleIds 拍品/标品id列表
     * @param int $saleType 拍品类型,1: 拍品, 2: 标品, 3: 直播间一口价
     * @param array
     */
    public function batchGetSaleExpressFeeTemplate($saleIds, $saleType = 1)
    {
        $data = [
            'saleIds' => $saleIds,
            'saleType' => intval($saleType),
        ];

        $result = $this->httpPost(Router::BATCH_GET_SALE_EXPRESS_FEE_TEMPLATE, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 通过模板id获取运费模板信息列表
     * @param int $expressFeeTemplateId 模板id
     * @param int $saleType 拍品类型,1: 拍品, 2: 标品, 3: 直播间一口价
     * @param int $saleStatus 拍品状态,1: 未出价, 2: 已出价, 3: 已截拍
     * @param array
     */
    public function getSaleExpressFeeTemplateList($expressFeeTemplateId, $saleType = 1, $saleStatus = 1)
    {
        $data = [
            'expressFeeTemplateId' => intval($expressFeeTemplateId),
            'saleType' => intval($saleType),
            'saleStatus' => intval($saleStatus),
        ];

        $result = $this->httpPost(Router::GET_SALE_EXPRESS_FEE_TEMPLATE_LIST, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 批量通过模板id获取运费模板信息列表
     * @param array $expressFeeTemplateIds 模板id列表
     * @param int $saleType 拍品类型,1: 拍品, 2: 标品, 3: 直播间一口价
     * @param int $saleStatus 拍品状态,1: 未出价, 2: 已出价, 3: 已截拍
     * @param int $limit 条数
     * @param int $offset 偏移量
     * @param array
     */
    public function batchGetSaleExpressFeeTemplateList($expressFeeTemplateIds, $saleType = 1, $saleStatus = 1, $limit = 1, $offset = 0)
    {
        $data = [
            'expressFeeTemplateIds' => $expressFeeTemplateIds,
            'saleType' => intval($saleType),
            'saleStatus' => intval($saleStatus),
            'limit' => intval($limit),
            'offset' => intval($offset),
        ];

        $result = $this->httpPost(Router::BATCH_GET_SALE_EXPRESS_FEE_TEMPLATE_LIST, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 新增运费模板信息
     * @param int $saleId 拍品/标品id
     * @param int $saleType 拍品类型,1: 拍品, 2: 标品, 3: 直播间一口价
     * @param int $expressFeeTemplateId 模板id
     * @param array
     */
    public function insertSaleExpressFeeTemplate($saleId, $saleType, $expressFeeTemplateId)
    {
        $data = [
            'saleId' => intval($saleId),
            'saleType' => intval($saleType),
            'expressFeeTemplateId' => intval($expressFeeTemplateId),
        ];

        return $this->httpPost(Router::INSERT_SALE_EXPRESS_FEE_TEMPLATE, $data);
    }

    /**
     * 更新运费模板信息
     * @param int $saleId 拍品/标品id
     * @param int $saleType 拍品类型,1: 拍品, 2: 标品, 3: 直播间一口价
     * @param int $expressFeeTemplateId 模板id
     * @param array $updateData 更新数据
     * @param array
     */
    public function updateSaleExpressFeeTemplate($saleId, $saleType, $expressFeeTemplateId, $updateData = null)
    {
        $data = [
            'saleId' => intval($saleId),
            'expressFeeTemplateId' => intval($expressFeeTemplateId),
            'saleType' => intval($saleType),
        ];

        if (!empty($updateData)) {
            $data['data'] = json_encode($updateData);
        }

        return $this->httpPost(Router::UPDATE_SALE_EXPRESS_FEE_TEMPLATE, $data);
    }

    public function uri2SaleId($uri)
    {
        $data = [
            'Uri' => (string)$uri,
        ];

        $result = $this->httpPost(Router::URI_2_SALE_ID, $data);

        $res = $result['data'] ?? 0;
        if (empty($res)) {
            return 0;
        }

        return $res;
    }

    public function saleId2Uri($saleId)
    {
        if (!$saleId) {
            return '';
        }

        $data = [
            'SaleId' => (int)$saleId,
        ];

        $result = $this->httpPost(Router::SALE_ID_2_URI, $data);

        $res = $result['data'] ?? '';
        if (empty($res)) {
            return '';
        }

        return $res;
    }

    public function searchSaleList(array $fields, array $where, $order = '', $limit = 10, $offset = 0)
    {
        $data = [
            'columns' => $fields,
            'where' => $where ? json_encode($where, JSON_UNESCAPED_UNICODE) : "{}",
            'order' => $order,
            'limit' => $limit,
            'offset' => $offset,
        ];

        $result = $this->httpPost(Router::SEARCH_SALE_LIST, $data);

        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    /**
     * 获取用户拍品数量
     * @param int $userInfoId
     * @param array $status
     * @param int $idDel
     * @param array $where
     * @return int
     */
    public function getSaleCount(int $userInfoId, array $status, int $idDel, array $where = []): int
    {
        if (empty($status)) {
            return 0;
        }

        $data = [];
        $data['UserInfoId'] = $userInfoId;
        $data['Status'] = $status;
        $data['IsDel'] = $idDel;

        if (empty($where)) {
            $data['where'] = '';
        } else {
            $data['where'] = json_encode($where, JSON_UNESCAPED_UNICODE);
        }

        $result = $this->httpPost(Router::GET_SALE_COUNT, $data);
        if (!isset($result['data'])) {
            return 0;
        }
        return intval($result['data']);
    }

    public function getCount(int $userinfoId, array $status, int $isDel, $where = [])
    {
        $data = [];

        $data['UserInfoId'] = $userinfoId;
        $data['Status'] = $status;
        $data['IsDel'] = $isDel;

        if (empty($where)) {
            $data['Where'] = "";
        } else {
            $data['Where'] = json_encode($where, JSON_UNESCAPED_UNICODE);
        }

        $result = $this->httpPost(Router::SALE_GET_COUNT, $data);

        return $result['data'] ?? 0;
    }

    public function getPushSaleList(int $loginUserId, array $fields, int $page, int $pageSize)
    {
        $result = $this->httpPost(
            Router::SALE_GET_PUSH_SALE_LIST,
            [
                'fields' => $fields,
                'page' => $page,
                'pageSize' => $pageSize
            ],
            [
                'loginuserid' => $loginUserId,
            ]
        );

        if (!empty($result['data']) && is_string($result['data'])) {
            $result['data'] = json_decode($result['data'], true);
        }

        return $result;
    }

    public function userShopNewSaleList($userId, $score = '', $limit = 10)
    {
        $result = $this->httpPost(Router::SALE_USER_SHOP_NEW_SALE_LIST, [
            'UserInfoID' => (int)$userId,
            'Score' => (string)$score,
            'Limit' => (int)$limit
        ]);

        if (!empty($result['data']) && is_string($result['data'])) {
            $result['data'] = json_decode($result['data'], true);
        }

        return $result;
    }

    public function incrView($saleIdUri, $offset = 1)
    {
        $data = [];
        $data['SaleIdUri'] = (string)$saleIdUri;
        $data['Offset'] = $offset;

        $result = $this->httpPost(Router::SALE_INCR_VIEW, $data);
        return $result['data'] ?? 0;
    }

    public function getShopCategorySales($shopId, $cateId)
    {
        $data = [];
        $data['UserInfoId'] = (int)$shopId;
        $data['SecCategory'] = (int)$cateId;

        $result = $this->httpPost(Router::SALE_GET_SHOP_CATEGORY_SALES, $data);
        $res = $result['data'] ?? [];
        if (empty($res)) {
            return [];
        }

        $res = json_decode($res, true);

        return $res;
    }

    public function getUserSaleList(
        $userinfoId,
        int $isDel,
        array $status = [],
        array $fields = [],
        array $where = [],
        string $order = '',
        $limit = 10000,
        $offset = 0
    ) {
        $data = [];
        $data['UserInfoId'] = (int)$userinfoId;
        $data['Status'] = $status;
        $data['IsDel'] = $isDel;
        $data['Order'] = $order;
        $data['Limit'] = $limit;
        $data['Offset'] = $offset;

        if (in_array('profileJson', $fields)) {
            $fields[] = 'content';
        }
        $data['Columns'] = $fields;

        if (empty($where)) {
            $data['Where'] = "";
        } else {
            $data['Where'] = json_encode($where, JSON_UNESCAPED_UNICODE);
        }

        $result = $this->httpPost(Router::SALE_GET_USER_SALE_LIST, $data);
        $res = $result['data'] ?? [];
        if (empty($res)) {
            return [];
        }

        $res = json_decode($res, true);
        $res = $this->arrayToObject($res);

        //content放入profileJson中，兼容老表
        if (in_array('profileJson', $fields)) {
            foreach ($res as $v) {
                if (!empty($v)) {
                    $v->profile->content = $v->content ?? '';
                    $v->profileJson = json_encode($v->profile, JSON_UNESCAPED_UNICODE);
                }
            }
        }

        if (empty($res)) {
            return [];
        }

        return $res;
    }

    public function getOnSaleList(
        array $fields = [],
        array $where = [],
        string $order = '',
        $limit = 10,
        $offset = 0
    ) {
        if (empty($where)) {
            return [];
        }

        $data = [];
        $data['Columns'] = $fields;
        $data['Order'] = $order;
        $data['Limit'] = $limit;
        $data['Offset'] = $offset;

        if (empty($where)) {
            $data['Where'] = "";
        } else {
            $data['Where'] = json_encode($where, JSON_UNESCAPED_UNICODE);
        }

        $result = $this->httpPost(Router::SALE_GET_ON_SALE_LIST_BY_WHERE, $data);

        $res = $result['data'] ?? [];
        if (empty($res)) {
            return [];
        }

        $res = json_decode($res, true);

        return $res;
    }

    public function batchGetOnSaleListByUserinfoIds(
        array $fields,
        $userinfoIds = [],
        $order = '',
        $limit = 10,
        $offset = null,
        $where = []
    ) {
        $data = [];
        $data['Columns'] = $fields;
        $data['Order'] = $order;
        $data['Limit'] = $limit;
        $data['Offset'] = $offset;

        if (empty($where)) {
            $data['Where'] = "";
        } else {
            $data['Where'] = json_encode($where, JSON_UNESCAPED_UNICODE);
        }

        $newUserinfoIds = [];
        foreach ($userinfoIds as $k => $v) {
            $newUserinfoIds[$k] = (int)$v;
        }

        $data['UserInfoIds'] = $newUserinfoIds;

        $result = $this->httpPost(Router::SALE_GET_ON_SALE_LIST_BY_USER_INFO_IDS, $data);

        $res = $result['data'] ?? [];
        if (empty($res)) {
            return [];
        }

        $res = json_decode($res, true);

        foreach ($res as $k => $v) {
            $res[$k] = $this->arrayToObject($v);
        }

        return $res;
    }

    public function getOnSaleListByDepotId(int $userinfoId, array $fields = [], array $where = [])
    {
        $data = [];
        $data['UserInfoId'] = $userinfoId;

        if (in_array('profileJson', $fields)) {
            $fields[] = 'content';
        }

        $data['Columns'] = $fields;

        if (empty($where)) {
            $data['Where'] = "";
        } else {
            $data['Where'] = json_encode($where, JSON_UNESCAPED_UNICODE);
        }

        $result = $this->httpPost(Router::SALE_GET_ON_SALE_LIST_BY_DEPOT_ID, $data);
        $res = $result['data'] ?? [];
        if (empty($res)) {
            return [];
        }

        $res = json_decode($res, true);
        $res = $this->arrayToObject($res);

        //content放入profileJson中，兼容老表
        if (in_array('profileJson', $fields)) {
            foreach ($res as $v) {
                if (!empty($v)) {
                    $v->profile->content = $v->content ?? '';
                    $v->profileJson = json_encode($v->profile, JSON_UNESCAPED_UNICODE);
                }
            }
        }

        return $res;
    }

    public function getOnSaleListByPdId(int $userinfoId, array $fields = [], array $where = [])
    {
        $data = [];
        $data['UserInfoId'] = $userinfoId;

        if (in_array('profileJson', $fields)) {
            $fields[] = 'content';
        }

        $data['Columns'] = $fields;

        if (empty($where)) {
            $data['Where'] = "";
        } else {
            $data['Where'] = json_encode($where, JSON_UNESCAPED_UNICODE);
        }

        $result = $this->httpPost(Router::SALE_GET_ON_SALE_LIST_BY_PD_ID, $data);
        $res = $result['data'] ?? [];
        if (empty($res)) {
            return [];
        }

        $res = json_decode($res, true);
        $res = $this->arrayToObject($res);

        //content放入profileJson中，兼容老表
        if (in_array('profileJson', $fields)) {
            foreach ($res as $v) {
                if (!empty($v)) {
                    $v->profile->content = $v->content ?? '';
                    $v->profileJson = json_encode($v->profile, JSON_UNESCAPED_UNICODE);
                }
            }
        }

        return $res;
    }

    public function getOnSaleListByDepotUserId(int $userinfoId, array $fields = [], array $where = [])
    {
        $data = [];
        $data['UserInfoId'] = $userinfoId;

        if (in_array('profileJson', $fields)) {
            $fields[] = 'content';
        }

        $data['Columns'] = $fields;

        if (empty($where)) {
            $data['Where'] = "";
        } else {
            $data['Where'] = json_encode($where, JSON_UNESCAPED_UNICODE);
        }

        $result = $this->httpPost(Router::SALE_GET_ON_SALE_LIST_BY_DEPOT_USER_ID, $data);
        $res = $result['data'] ?? [];
        if (empty($res)) {
            return [];
        }

        $res = json_decode($res, true);
        $res = $this->arrayToObject($res);

        //content放入profileJson中，兼容老表
        if (in_array('profileJson', $fields)) {
            foreach ($res as $v) {
                if (!empty($v)) {
                    $v->profile->content = $v->content ?? '';
                    $v->profileJson = json_encode($v->profile, JSON_UNESCAPED_UNICODE);
                }
            }
        }

        return $res;
    }

    public function getNotPayBzjSale($goodsId, array $fields = [])
    {
        $data = [];
        $data['DraftId'] = (int)$goodsId;
        $data['IsDel'] = 0;
        $data['Order'] = '';
        $data['Limit'] = 1;
        $data['Offset'] = 0;

        if (in_array('profileJson', $fields)) {
            $fields[] = 'content';
        }

        $data['Columns'] = $fields;

        $result = $this->httpPost(Router::SALE_GET_NOT_PAY_BZJ_SALE_BY_DRAFT_ID, $data);
        $res = $result['data'] ?? [];
        if (empty($res) || empty($res[0])) {
            return [];
        }

        $res = json_decode($res, true);
        $res = $this->arrayToObject($res);

        //content放入profileJson中，兼容老表
        if (in_array('profileJson', $fields)) {
            foreach ($res as $v) {
                if (!empty($v)) {
                    $v->profile->content = $v->content ?? '';
                    $v->profileJson = json_encode($v->profile, JSON_UNESCAPED_UNICODE);
                }
            }
        }

        return $res[0];
    }

    public function getSaleByDraftId($userinfoId, $draftId, $saleType = 0)
    {
        if (empty($userinfoId) || empty($draftId)) {
            return null;
        }

        $result = $this->httpPost(Router::SALE_GET_SALE_BY_DRAFT_ID, [
            'UserInfoId' => (int)$userinfoId,
            'DraftId' => (int)$draftId,
            'SaleType' => (int)$saleType
        ]);

        $res = $result['data'] ?? [];
        if (empty($res)) {
            return null;
        }

        $res = json_decode($res);

        if (is_array($res)) {
            $res = $this->arrayToObject($res);
        }

        $res->profile = $this->arrayToObject(json_decode($res->profileJson, JSON_UNESCAPED_UNICODE));

        return $res;
    }

    public function getSaleListByDraftId(
        int $userinfoId,
        int $draftId,
        int $saleType,
        array $columns,
        array $filter = [],
        string $order = '',
        int $limit = 10,
        int $offset = 0
    ) {
        $data = [
            'UserInfoId' => $userinfoId,
            'DraftId' => $draftId,
            'SaleType' => $saleType,
            'Columns' => $columns,
            'Filter' => empty($filter) ? '' : json_encode($filter, JSON_UNESCAPED_UNICODE),
            'Order' => $order,
            'Limit' => $limit,
            'Offset' => $offset,
        ];

        $result = $this->httpPost(Router::SALE_GET_SALE_LIST_BY_DRAFT_ID, $data);
        $this->dealResultData($result, $this->formatResult());

        return $result;
    }

    public function getShopDetailOfSale($userinfoId, $recommendSaleIds = [])
    {
        if (!$userinfoId) {
            return null;
        }

        if (empty($recommendSaleIds)) {
            $recommendSaleIds = [];
        }

        $idUris = array_map(function ($idUri) {
            return (string)$idUri;
        }, $recommendSaleIds);

        $idUris = array_values($idUris);

        $data = [];
        $data['UserInfoId'] = $userinfoId;
        $data['RecommendSaleIds'] = $idUris;

        $result = $this->httpPost(Router::SALE_GET_SHOP_DETAIL_OF_SALE, $data);
        $res = $result['data'] ?? [];
        if (empty($res)) {
            return null;
        }

        $data = $this->arrayToObject($res);

        if ($data->onSaleSales) {
            $data->onSale = json_decode($data->onSaleSales, true);
        } else {
            $data->onSale = null;
        }
        unset($data->onSaleSales);

        if ($data->previewSales) {
            $data->previewSale = json_decode($data->previewSales, true);
        } else {
            $data->previewSale = null;
        }
        unset($data->previewSales);

        if ($data->recommendSales) {
            $data->recommendList = json_decode($data->recommendSales, true);
        } else {
            $data->recommendList = null;
        }
        unset($data->recommendSales);

        return $data;
    }

    /**
     * @param array $imgKeys
     * @return array
     */
    public function queryImgMd5(array $imgKeys)
    {
        $data = ['Images' => $imgKeys];

        $result = $this->httpPost(Router::SALE_QUERY_IMG_MD5, $data);
        if (empty($result['data'])) {
            return [];
        }

        return json_decode($result['data'], true);
    }
}
