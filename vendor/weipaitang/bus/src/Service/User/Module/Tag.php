<?php

namespace WptBus\Service\User\Module;

use WptBus\Service\BaseService;
use WptBus\Service\User\Router;

/**
 * 登录相关
 */
class Tag extends BaseService
{

    /**
     * 创建标签
     * @param int $groupId
     * @param string $businessUniqueId
     * @param string $tagName
     * @param string $tagDescription
     * @param string $tagData
     * @return array|void
     *
     * {
     * "tagId":371  // 标签ID
     * }
     *
     */
    public function create(int $groupId, string $businessUniqueId, string $tagName, string $tagDescription, string $tagData)
    {
        return $this->httpPost(Router::CREATE_TAG, [
            "groupId" => $groupId,
            "tagName" => $tagName,
            "businessUniqueId" => $businessUniqueId,
            "tagDescription" => $tagDescription,
            "tagData" => $tagData,
        ]);
    }

    /**
     * 修改标签
     * @param int $tagId
     * @param string $businessUniqueId
     * @param string $tagName
     * @param string $tagDescription
     * @param string $tagData
     * @return array|void
     *
     */
    public function update(int $tagId, string $businessUniqueId, string $tagName, string $tagDescription, string $tagData)
    {
        return $this->httpPost(Router::UPDATE_TAG, [
            "tagId" => $tagId,
            "tagName" => $tagName,
            "businessUniqueId" => $businessUniqueId,
            "tagDescription" => $tagDescription,
            "tagData" => $tagData,
        ]);
    }

    /**
     * 删除标签
     * @param int $tagId
     * @param string $businessUniqueId
     * @return array|void
     *
     */
    public function delete(int $tagId, string $businessUniqueId)
    {
        return $this->httpPost(Router::DELETE_TAG, [
            "tagId" => $tagId,
            "businessUniqueId" => $businessUniqueId,
        ]);
    }

    /**
     * 创建标签组
     * @param string $businessUniqueId
     * @param string $groupName
     * @param string $groupDescription
     * @param string $groupData
     * @return array|void
     *
     * {
     * "groupId":371  // 标签组ID
     * }
     *
     */
    public function createGroup(string $businessUniqueId, string $groupName, string $groupDescription, string $groupData)
    {
        return $this->httpPost(Router::CREATE_TAG_GROUP, [
            "businessUniqueId" => $businessUniqueId,
            "groupName" => $groupName,
            "groupDescription" => $groupDescription,
            "groupData" => $groupData,
        ]);
    }

    /**
     * 修改标签组
     * @param int $groupId
     * @param string $businessUniqueId
     * @param string $groupName
     * @param string $groupDescription
     * @param string $groupData
     * @return array|void
     *
     */
    public function updateGroup(int $groupId, string $businessUniqueId, string $groupName, string $groupDescription, string $groupData)
    {
        return $this->httpPost(Router::UPDATE_TAG_GROUP, [
            "groupId" => $groupId,
            "businessUniqueId" => $businessUniqueId,
            "groupName" => $groupName,
            "groupDescription" => $groupDescription,
            "groupData" => $groupData,
        ]);
    }

    /**
     * 删除标签组
     * @param int $groupId
     * @param string $businessUniqueId
     * @return array|void
     *
     */
    public function deleteGroup(int $groupId, string $businessUniqueId)
    {
        return $this->httpPost(Router::DELETE_TAG_GROUP, [
            "groupId" => $groupId,
            "businessUniqueId" => $businessUniqueId,
        ]);
    }

    /**
     * 标签更换分组
     * @param int $tagId
     * @param int $groupId
     * @param string $businessUniqueId
     * @return array|void
     *
     */
    public function changeGroup(int $tagId, int $groupId, string $businessUniqueId)
    {
        return $this->httpPost(Router::CHANGE_GROUP_TAG, [
            "tagId" => $tagId,
            "groupId" => $groupId,
            "businessUniqueId" => $businessUniqueId,
        ]);
    }

    /**
     * 批量绑定
     * @param int $tagId
     * @param string $businessUniqueId
     * @param array $entityIds
     * @return array|void
     *
     */
    public function batchBind(int $tagId, string $businessUniqueId, array $entityIds)
    {
        return $this->httpPost(Router::BATCH_BIND_TAG, [
            "tagId" => $tagId,
            "businessUniqueId" => $businessUniqueId,
            "entityIds" => $entityIds,
        ]);
    }

    /**
     * 批量解绑绑定
     * @param int $tagId
     * @param string $businessUniqueId
     * @param array $entityIds
     * @return array|void
     *
     */
    public function batchUnbind(int $tagId, string $businessUniqueId, array $entityIds)
    {
        return $this->httpPost(Router::BATCH_UNBIND_TAG, [
            "tagId" => $tagId,
            "businessUniqueId" => $businessUniqueId,
            "entityIds" => $entityIds,
        ]);
    }

    /**
     * 分组返回指定业务的所有标签
     * @param string $businessUniqueId
     * @return array|void
     * "tags": [
     *    {
     *        "tagId": 1,
     *        "tagName": "user-group-1",
     *        "tagDescription": "",
     *        "tagData": "",
     *        "tags": [
     *        {
     *            "tagId": 2,
     *            "tagName": "user-tag-1-update",
     *            "tagDescription": "",
     *            "tagData": "{}"
     *        }
     *        ]
     *    }
     *    ]
     */
    public function tagListByBusinessUniqueId(string $businessUniqueId)
    {
        $ret = $this->httpPost(Router::TAG_LIST_BY_BUSINESS, [
            "businessUniqueId" => $businessUniqueId,
        ]);

        $this->dealResultData($ret, function ($data) {
            return $data ? json_decode($data) : [];
        });
        return $ret;
    }

    /**
     * 分组返回实体绑定的所有标签
     * @param string $businessUniqueId
     * @return array|void
     * "tags": [
     *    {
     *        "tagId": 1,
     *        "tagName": "user-group-1",
     *        "tagDescription": "",
     *        "tagData": "",
     *        "tags": [
     *        {
     *            "tagId": 2,
     *            "tagName": "user-tag-1-update",
     *            "tagDescription": "",
     *            "tagData": "{}"
     *        }
     *        ]
     *    }
     *    ]
     */
    public function bindListByEntityId(string $businessUniqueId, int $entityId)
    {
        $ret = $this->httpPost(Router::BIND_LIST_BY_ENTITY_ID, [
            "businessUniqueId" => $businessUniqueId,
            "entityId" => $entityId,
        ]);

        $this->dealResultData($ret, function ($data) {
            return $data ? json_decode($data) : [];
        });
        return $ret;
    }

    /**
     * 获取标签对应的所有实体ID
     * @param int $tagId
     * @param int $page
     * @param int $pageSize
     * @return array|void
     * {
     *   "total": 115,
     *   "count": 2,
     *   "page": 0,
     *   "entityIds": [
     *     47682920,
     *     47829266,
     *     ]
     * }
     */
    public function getEntityIdsByTagId(int $tagId, int $page, int $pageSize)
    {
        return $this->httpPost(Router::GET_ENTITY_IDS_BY_TAG_ID, [
            "tagId" => $tagId,
            "page" => $page,
            "pageSize" => $pageSize,
        ]);
    }

    public function sync(int $id, $groupId, string $tagName, string $businessUniqueId, string $tagDescription, string $tagData)
    {
        return $this->httpPost(Router::SYNC_TAG, [
            "id" => $id,
            "groupId" => $groupId,
            "tagName" => $tagName,
            "businessUniqueId" => $businessUniqueId,
            "tagDescription" => $tagDescription,
            "tagData" => $tagData,
        ]);
    }

    public function syncBatchBind(int $tagId, $bindTime, string $businessUniqueId, array $entityIds)
    {
        return $this->httpPost(Router::SYNC_BATCH_BIND_TAG, [
            "tagId" => $tagId,
            "bindTime" => $bindTime,
            "businessUniqueId" => $businessUniqueId,
            "entityIds" => $entityIds,
        ]);
    }

    public function syncBindTags(int $entityId, string $businessUniqueId, string $tagId2bindTime)
    {
        return $this->httpPost(Router::SYNC_BIND_TAGS, [
            "entityId" => $entityId,
            "businessUniqueId" => $businessUniqueId,
            "tagId2bindTime" => $tagId2bindTime,
        ]);
    }

    public function syncUnbindTags(int $entityId, string $businessUniqueId, array $tagIds)
    {
        return $this->httpPost(Router::SYNC_UNBIND_TAGS, [
            "entityId" => $entityId,
            "businessUniqueId" => $businessUniqueId,
            "tagIds" => $tagIds,
        ]);
    }

}
