<?php


namespace WptBus\Service\User\Module;

use WptBus\Lib\Utils;
use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class UserExtend extends BaseService
{
    /**
     * @var array
     */
    protected $defaultInt = [
        'isSeller', 'recommendable', 'goodshopable'
    ];

    /**
     * @var array
     */
    protected $defaultString = [
        'shopRuleJson', 'pdLibJson', 'deliveryComJson', 'apitokenJson',
        'bnpJson', 'scopedCategories'
    ];

    /**
     * 通过 uid 获取用户扩展信息 （迁移user-service sdk）
     * @param int $uid
     * @param array $fields
     * @param bool $useBindId
     * @return array
     */
    public function getUserExtendByUid(int $uid, array $fields, bool $useBindId = true): array
    {
        if ($uid <= 0 || empty($fields) || strlen($uid) > 9) {
            return [];
        }
        $fields = array_unique($fields);
        $res = $this->httpPost(Router::GET_USER_EXTEND_BY_UID, [
            'uid' => $uid,
            'fields' => array_values($fields),
            'useBindId' => $useBindId,
        ]);
        if (!isset($res['code']) || $res['code'] >= 200000) {
            return [];
        }

        // rhx
        if (empty($res["data"]["userinfoId"])) {
            return [];
        }

        // rhx
        $result = [];
        foreach ($fields as $field) {
            if (!isset($res["data"][$field])) {
                if (in_array($field, $this->defaultInt)) {
                    $result[$field] = 0;
                } elseif (in_array($field, $this->defaultString)) {
                    $result[$field] = "";
                }
            } else {
                $result[$field] = $res["data"][$field];
            }
        }
        return $result ?? [];
    }

    /**
     * 更新用户扩展信息（迁移user-service sdk）
     * @param int $shopId
     * @param array $data
     * @return int
     */
    public function updateUserExtend(int $shopId, array $data): int
    {
        if ($shopId <= 0 || empty($data)) {
            return 0;
        }

        $reqData = [];
        foreach ($data as $key => $val) {
            if (Utils::str_contains($key, 'Json')) {
                $reqData[$key] = json_decode($val, true);
            } else if ($key == 'scopedCategories') {
                if (is_array($val)) {
                    $reqData[$key] = implode(",", $val);
                } else {
                    $reqData[$key] = $val;
                }
            } else {
                $reqData[$key] = $val;
            }
        }

        $res = $this->httpPost(Router::UPDATE_USER_EXTEND, [
            'uid' => $shopId,
            'data' => json_encode($reqData, JSON_UNESCAPED_UNICODE),
        ]);
        if (!isset($res['code']) || $res['code'] >= 200000) {
            return 0;
        }
        return $res['data'];
    }
}