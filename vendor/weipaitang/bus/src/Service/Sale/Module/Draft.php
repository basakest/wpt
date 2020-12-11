<?php

namespace WptBus\Service\Sale\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Sale\Router;

class Draft extends BaseService
{
    protected function formatResult()
    {
        return function ($data) {
            if (!empty($data) && is_string($data)) {
                return json_decode($data, true);
            }
            return [];
        };
    }

    /**
     * @param int $goodsId
     * @param array $fields
     * @return array|mixed
     */
    public function get(int $goodsId, array $fields)
    {
        $result = $this->httpPost(Router::DRAFT_GET_DRAFT, ['draftId' => $goodsId, 'fields' => $fields]);

        if (!empty($result['data']['goods'])) {
            $goods = json_decode($result['data']['goods']);
            if (!empty($goods->profileJson)) {
                $goods->profile = json_decode($goods->profileJson);
                $goods->profile->content = $goods->content;
                if (!empty($goods->profile->secCategoryTemplate)) {
                    if (is_string($goods->profile->secCategoryTemplate)) {
                        $goods->profile->secCategoryTemplate = json_decode(
                            $goods->profile->secCategoryTemplate,
                            true
                        );
                    } else {
                        $goods->profile->secCategoryTemplate = json_decode(json_encode(
                            $goods->profile->secCategoryTemplate,
                            JSON_UNESCAPED_UNICODE
                        ), true);
                    }
                }
            }

            return $goods;
        }
        return null;
    }

    /**
     * @param int $goodsId
     * @param array $data
     * @return int
     */
    public function update(int $goodsId, array $data)
    {
        $result = $this->httpPost(
            Router::DRAFT_UPDATE_DRAFT,
            [
                'draftId' => $goodsId,
                'Data' => json_encode($data, JSON_UNESCAPED_UNICODE)
            ],
            [
                'loginuserid' => 0,
            ]
        );

        if (!empty($result['data']['number'])) {
            return $result['data']['number'];
        }

        return 0;
    }

    /**
     * @param array $params
     * @return int
     */
    public function insert(array $params)
    {
        if (!empty($params['userinfoId'])) {
            $params['userinfoId'] = (int)$params['userinfoId'];
        }
        $insertData = json_encode($params, JSON_UNESCAPED_UNICODE);
        $result = $this->httpPost(Router::DRAFT_INSERT_DRAFT, ['insertData' => $insertData]);

        if (!empty($result['data']['goodsId'])) {
            return $result['data']['goodsId'];
        }

        return 0;
    }

    /**
     * @param array $params
     * @return array
     */
    public function batchInsert(array $params)
    {
        foreach ($params as $k => $param) {
            if (!empty($param['userinfoId'])) {
                $param['userinfoId'] = (int)$param['userinfoId'];
            }
            $params[$k] = $param;
        }
        $insertData = json_encode($params, JSON_UNESCAPED_UNICODE);
        $result = $this->httpPost(Router::DRAFT_BATCH_INSERT_DRAFT, ['insertData' => $insertData]);

        if (!empty($result['data']['draftIds'])) {
            return $result['data']['draftIds'];
        }
        return [];
    }

    /**
     * @param int $loginUserId
     * @return bool
     */
    public function delete(int $loginUserId, int $goodsId)
    {
        $result = $this->httpPost(
            Router::DRAFT_DELETE_DRAFT,
            ['draftId' => $goodsId],
            [
                'loginuserid' => $loginUserId,
            ]
        );

        if (!empty($result['data']['status'])) {
            return true;
        }
        return false;
    }

    /**
     * @param int $loginUserId
     * @return bool
     */
    public function multiDelete(int $loginUserId)
    {
        $result = $this->httpPost(
            Router::DRAFT_MULTI_DELETE_GOODS,
            ['userId' => $loginUserId],
            [
                'loginuserid' => $loginUserId,
            ]
        );

        if ($result['code'] == 0) {
            return true;
        }
        return false;
    }

    /**
     * @param int $loginUserId
     * @param string $score
     * @param int $limit
     * @return array
     */
    public function getList(int $loginUserId, string $score = '', int $limit = 10)
    {
        $result = $this->httpPost(
            Router::DRAFT_GET_DRAFT_LIST,
            [
                'userId' => $loginUserId,
                'score'  => $score,
                'limit'  => $limit
            ],
            [
                'loginuserid' => $loginUserId,
            ]
        );

        if (!empty($result['data']['list'])) {
            $list = json_decode($result['data']['list'], true);
            foreach ($list as $k => $v) {
                $v['profile'] = json_decode($v['profileJson'], true);
                unset($v['profileJson']);
                $list[$k] = $v;
            }
            return $list;
        }
        return [];
    }

    /**
     * @param int $loginUserId
     * @param string $score
     * @param int $limit
     * @return array
     */
    public function getUnitaryList(int $loginUserId, string $score = '', int $limit = 10)
    {
        $result = $this->httpPost(
            Router::DRAFT_GET_UNITARY_LIST,
            [
                'userId' => $loginUserId,
                'score'  => $score,
                'limit'  => $limit
            ],
            [
                'loginuserid' => $loginUserId,
            ]
        );

        if (!empty($result['data']['list'])) {
            $list = json_decode($result['data']['list'], true);
            foreach ($list as $k => $v) {
                $v['profile'] = json_decode($v['profileJson'], true);
                unset($v['profileJson']);
                $list[$k] = $v;
            }
            return $list;
        }
        return [];
    }

    /**
     * @param int $loginUserId
     * @param string $score
     * @param int $limit
     * @return array
     */
    public function unionGetList(int $loginUserId, string $score, int $limit)
    {
        $result = $this->httpPost(
            Router::DRAFT_UNION_GET_DRAFT_LIST,
            [
                'userId' => $loginUserId,
                'score'  => $score,
                'limit'  => $limit
            ],
            [
                'loginuserid' => $loginUserId,
            ]
        );

        if (!empty($result['data']['list'])) {
            $list = json_decode($result['data']['list'], true);
            foreach ($list as $k => $v) {
                $v['profile'] = json_decode($v['profileJson'], true);
                unset($v['profileJson']);
                $list[$k] = $v;
            }
            return $list;
        }
        return [];
    }
}
