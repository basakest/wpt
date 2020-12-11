<?php


namespace WptBus\Service\Sale\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Sale\Router;

class Author extends BaseService
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
     * 获取作者列表
     * @param array $cols
     * @param array $where
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getList(array $cols, array $where, string $order, int $limit, int $offset)
    {
        $data = [
            'Columns' => $cols,
            'Where' => json_encode($where, JSON_UNESCAPED_UNICODE),
            'Order' => $order,
            'Limit' => $limit,
            'Offset' => $offset
        ];
        if (empty($where)) {
            $data['Where'] = '';
        }

        return $this->httpPost(Router::AUTHOR_GET_LIST, $data);
    }

    /**
     * 获取作者数量
     * @param array $where
     * @return array
     */
    public function getAuthorCount(array $where)
    {
        $data = [
            "Where" => json_encode($where, JSON_UNESCAPED_UNICODE),
        ];
        if (empty($where)) {
            $data['Where'] = '';
        }

        return $this->httpPost(Router::AUTHOR_GET_COUNT, $data);
    }

    /**
     * 更新作者状态
     * @param int $id
     * @param int $status
     * @return array
     */
    public function updateStatus(int $id, int $status)
    {
        $data = [
            'Id' => $id,
            'Status' => $status
        ];
        return $this->httpPost(Router::AUTHOR_UPDATE_STATUS, $data);
    }

    /**
     * 创建新作者
     * @param string $name 作者名称
     * @param int $unit 作者单位
     * @param int $authorStatus
     * @param array $extendJson 扩展字段
     * @return array
     */
    public function CreateAuthor(string $name, int $unit, int $authorStatus, array $extendJson = [])
    {
        $data = [
            'Name' => $name,
            'Unit' => $unit,
            'Status' => $authorStatus,
            'ExtendJson' => json_encode($extendJson, JSON_UNESCAPED_UNICODE)
        ];
        return $this->httpPost(Router::AUTHOR_CREATE_AUTHOR, $data);
    }

    /**
     * 获取艺术家类型
     * @param string $name
     * @return array
     */
    public function getAuthorIdentity(string $name)
    {
        $data = [
            'Author' => $name
        ];
        return $this->httpPost(Router::AUTHOR_GET_AUTHOR_IDENTITY, $data);
    }

}
