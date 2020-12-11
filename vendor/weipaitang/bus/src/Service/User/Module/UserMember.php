<?php


namespace WptBus\Service\User\Module;

use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

class UserMember extends BaseService
{
    /**
     * 更新会员积分
     * @param int $uid
     * @param int $score 减分为负数
     * @param string $desc 描述
     * @param string $target sale：拍品 yj：有匠 yuncang：云仓
     * @param int $targetId 关联id
     * @return array
     */
    public function syncMemberScores(int $uid, int $score, string $desc, string $target, int $targetId)
    {
        $data = ["uid" => intval($uid), "score" => intval($score), "desc" => $desc, "target" => $target, "targetId" => intval($targetId)];
        if ($error = $this->validate($data,
            ["uid" => "required|int", "targetId" => "required|int", "target" => "required|in:sale,yj,yuncang"])) {
            return $error;
        }
        return $this->httpPost(Router::MEMBER_SYNC_MEMBER_SCORES, $data);
    }

    /**
     * 获取会员成长记录列表
     * @param int $uid
     * @param array $where 例：15天内 json_encode(['createTime > '=> time() - 3600 * 24 * 15])
     * @param int $pageNum
     * @param int $pageSize
     * @param string $orderBy
     * @param array $fields
     * @return array
     */
    public function getMemberGrowthLogList(
        int $uid,
        array $where = [],
        int $pageNum = 1,
        int $pageSize = 30,
        string $orderBy = "",
        array $fields = []
    ) {
        $data = array(
            "uid" => intval($uid),
            "where" => (string)json_encode($where),
            "pageNum" => (int)$pageNum,
            "pageSize" => (int)$pageSize,
            "orderBy" => (string)$orderBy,
            "fields" => (array)$fields,
        );
        if ($error = $this->validate($data,
            ["uid" => "required|int"])) {
            return $error;
        }
        $ret = $this->httpPost(Router::MEMBER_GET_MEMBER_GROWTH_LOG_LIST, $data);
        $this->dealResultData($ret, function ($data) {
            if ($data) {
                $list = [];
                foreach ($data as $val) {
                    $list[] = (object)$val;
                }
                return $list;
            }
            return $data;
        });
        return $ret;
    }

    /**
     * 获取成长值列表
     * @param array $params
     * @return array
     */
    public function GetMemberGrowthLogListByFields(array $params): array
    {
        $defaultParams = [
            'Target' => -1,  //关联类型 0 none，1 sale，2 yj
            'TargetId' => 0, //TargetId 关联ID
            'Uid' => -1,     //Uid   用户id
        ];

        if (isset($params['Target'])) {
            $defaultParams['Target'] = (int)$params['Target'];
        }
        if (isset($params['TargetId'])) {
            $defaultParams['TargetId'] = (int)$params['TargetId'];
        }
        if (isset($params['Uid'])) {
            $defaultParams['Uid'] = (int)$params['Uid'];
        }
        return $this->httpPost(Router::MEMBER_GET_MEMBER_GROWTH_LOG_LIST_BY_FIELDS, $defaultParams);
    }

    /**
     * 是否增加过积分，当前只能在描述desc为"确认收货"时用
     * @param int $uid
     * @param int $targetId
     * @return array|mixed
     */
    public function hasIncreasedScores(int $uid, int $targetId)
    {
        $data = array("uid" => intval($uid), "targetId" => intval($targetId));
        if ($error = $this->validate($data, ["uid" => "required|int", "targetId" => "required|int"])) {
            return $error;
        }
        return $this->httpPost(Router::MEMBER_IS_INCREASED_SCORES, $data);
    }

    /**
     * 开通会员
     * @param int $uid
     * @return array|mixed
     */
    public function openMember(int $uid)
    {
        $data = ["uid" => intval($uid)];
        if ($error = $this->validate($data, ["uid" => "required|int"])) {
            return $error;
        }
        return $this->httpPost(Router::MEMBER_OPEN_MEMBER, $data);
    }
}