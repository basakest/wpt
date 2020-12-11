<?php


namespace WptBus\Service\Sale\Module;

use WptBus\Lib\Error;
use WptBus\Lib\Response;
use WptBus\Lib\Utils;
use WptBus\Service\BaseService;
use WptBus\Service\Sale\Consts\GoodsConst;
use WptBus\Service\Sale\Router;
use WptCommon\Library\Facades\MLogger;

class Goods extends BaseService
{


    /**
     * @param array $param
     * @return array|mixed
     */
    public function create(array $param)
    {
        $data = [];
        if (!empty($param['id'])) {
            $data['id'] = (int)$param['id'];
        }
        if (!empty($param['uri'])) {
            $data['uri'] = (string)$param['uri'];
        }
        if (!empty($param['type'])) {
            $data['type'] = (int)$param['type'];
        }
        if (isset($param['secCategory'])) {
            $data['secCategory'] = (int)$param['secCategory'];
        } else {
            $data['secCategory'] = -1;
        }
        if (isset($param['category'])) {
            $data['category'] = (int)$param['category'];
        } else {
            $data['category'] = -1;
        }
        if (!empty($param['imgs'])) {
            if (is_array($param['imgs']) || is_object($param['imgs'])) {
                $data['imgs'] = json_encode($param['imgs']);
            } else {
                $data['imgs'] = (string)$param['imgs'];
            }
        }
        if (!empty($param['profileJson'])) {
            if (is_array($param['profileJson']) || is_object($param['profileJson'])) {
                $data['profileJson'] = json_encode($param['profileJson'], JSON_UNESCAPED_UNICODE);
            } else {
                $data['profileJson'] = (string)$param['profileJson'];
            }
        }
        if (!empty($param['businessType'])) {
            $data['businessType'] = (int)$param['businessType'];
        }
        if (!empty($param['content'])) {
            $data['content'] = (string)$param['content'];
        }
        if (!empty($param['title'])) {
            $data['title'] = (string)$param['title'];
        }
        if (!empty($param['status'])) {
            $data['goodsStatus'] = (int)$param['status'];
        }
        if (!empty($param['price'])) {
            $data['price'] = (int)$param['price'];
        }
        if (!empty($param['enableReturn'])) {
            $data['enableReturn'] = (int)$param['enableReturn'];
        }
        if (!empty($param['expressFee'])) {
            $data['expressFee'] = (string)$param['expressFee'];
        }

        if (!empty($param['stock'])) {
            $data['stock'] = (int)$param['stock'];
        }
        if (!empty($param['buyLimit'])) {
            $data['buyLimit'] = (int)$param['buyLimit'];
        }
        if (!empty($param['sortTime'])) {
            $data['sortTime'] = (int)$param['sortTime'];
        }
        if (!empty($param['sellTime'])) {
            $data['sellTime'] = (int)$param['sellTime'];
        }
        if (isset($param['isBuyLimit'])) {
            $data['isBuyLimit'] = (int)$param['isBuyLimit'];
        }
        if (!empty($param['userinfoId'])) {
            $data['userinfoId'] = (int)$param['userinfoId'];
        }
        $ret = $this->httpPost(Router::CREATE_GOODS, $data);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param $id
     * @return array
     */
    public function delete(int $id)
    {
        if ($error = $this->validate(['id' => $id], ['id' => 'required'])) {
            return $error;
        }
        return $this->update([$id], [], ['isDel' => 1]);
    }

    /**
     * @param int $id
     * @return array
     */
    public function goodsUpById(int $id)
    {
        return $this->update([$id], [], ['status' => GoodsConst::UP_STATUS, 'lastStatusTime' => time()]);
    }

    /**
     * @param array $ids
     * @return array
     */
    public function goodsUp(array $ids)
    {
        return $this->update($ids, [], ['status' => GoodsConst::UP_STATUS, 'lastStatusTime' => time()]);
    }

    /**
     * @param int $id
     * @return array
     */
    public function goodsDownById(int $id)
    {
        return $this->update([$id], [], ['status' => GoodsConst::DOWN_STATUS, 'lastStatusTime' => time(), 'sortTime' => 0]);
    }

    /**
     * @param array $ids
     * @return array
     */
    public function goodsDownByIds(array $ids)
    {
        return $this->update($ids, [], ['status' => GoodsConst::DOWN_STATUS, 'lastStatusTime' => time(), 'sortTime' => 0]);
    }

    /**
     * @param int $id
     * @param $sortType
     * @return array
     */
    public function setGoodsTop(int $id, $sortType)
    {
        if ($error = $this->validate(['id' => $id], ['id' => 'required'])) {
            return $error;
        }
        $sortTime = empty($sortType) ? 0 : time();
        return $this->update([$id], [], ['sortTime' => $sortTime]);
    }


    /**
     * @param $id
     * @param $data
     * @return array
     */
    public function updateById(int $id, array $data)
    {
        if ($error = $this->validate(['id' => $id], ['id' => 'required'])) {
            return $error;
        }
        return $this->update([$id], [], $data);
    }

    /**
     * @param $uri
     * @param $data
     * @return array
     */
    public function updateByUri(string $uri, array $data)
    {
        if ($error = $this->validate(['uri' => $uri], ['uri' => 'required'])) {
            return $error;
        }
        return $this->update([], [$uri], $data);
    }

    /**
     * @param array $id
     * @param array $uri
     * @param array $data
     * @return array
     */
    private function update(array $id, array $uri, array $data)
    {
        if (isset($data["isBuyLimit"]) && $data["isBuyLimit"] == 0) {
            $data["buyLimit"] = 0;
        }
        if (isset($data['imgs'])) {
            if (is_array($data['imgs']) || is_object($data['imgs'])) {
                $data['imgs'] = json_encode($data['imgs']);
            }
        }
        if (isset($data['profileJson'])) {
            if (is_array($data['profileJson']) || is_object($data['profileJson'])) {
                $data['profileJson'] = json_encode($data['profileJson'], JSON_UNESCAPED_UNICODE);
            }
        }
        foreach ((array)$data as $k => $v) {
            $data[$k] = (string)$v;
        }

        $params["data"] = $data;
        $params["id"] = (array)$id;
        $params["uri"] = (array)$uri;
        $ret = $this->httpPost(Router::UPDATE_GOODS, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }


    /**
     * @param $id
     * @param $value
     * @return array
     */
    public function incrView($id, $value)
    {
        $params["id"] = (int)$id;
        $params["value"] = (int)$value;
        if ($error = $this->validate($params, ['id' => 'required'])) {
            return $error;
        }

        $ret = $this->httpPost(Router::INCR_VIEW, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param $id
     * @param $value
     * @param $sellNum
     * @return array
     */
    public function setStock($id, $value, $sellNum = 0)
    {
        $params["sellNum"] = (int)$sellNum;
        $params["goodsId"] = (int)$id;
        $params["value"] = (int)$value;
        if ($error = $this->validate($params, ['goodsId' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::SET_STOCK, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param $id
     * @param $uri
     * @param $fields
     * @param array $filter
     * @return array|void
     */
    public function getGoods($id, $uri, $fields, $filter = [])
    {
        if ($fields and $filter) {
            $fields = array_unique(array_merge((array)$fields, (array)array_keys($filter)));
        }
        $newFields =[];
        foreach ($fields as $v){
            if(in_array($v,['likes','shareNum','businessId','depotId','depotUserId'])){
                continue;
            }
            $newFields[] = $v;
        }
        $field = $newFields;
        $this->setIsBuyLimitFields($newFields);
        $params["id"] = (int)$id;
        $params["fields"] = array_values($newFields);
        $params["uri"] = $uri;
        if ($error = $this->validate($params, ['fields' => 'required|array|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_GOODS, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 0);
            return $data;
        });
        if ($ret['code'] == 0 && !empty($ret['data'])) {
            if (in_array('isBuyLimit', $field)) {
                $ret['data']->isBuyLimit = $ret['data']->buyLimit > 0 ? 1 : 0;
                $ret['data']->buyLimit = $ret['data']->buyLimit == 0 ? 1 : $ret['data']->buyLimit;
            }
            $ret['data'] = Utils::filterResult($ret['data'], $filter);
        }
        return $ret;
    }

    /**
     * @param int $id
     * @param array $fields
     * @param array $filter
     * @return array|void
     */

    public function getGoodsById(int $id, array $fields, $filter = [])
    {
        return $this->getGoods($id, '', $fields, $filter);
    }

    /**
     * @param string $uri
     * @param array $fields
     * @param array $filter
     * @return array|void
     */

    public function getGoodsByUri(string $uri, array $fields, $filter = [])
    {
        if (!in_array('id', $fields)) {
            array_push($fields, 'id');
        }
        return $this->getGoods(0, $uri, $fields, $filter);
    }

    /**
     * @param $where
     * @param $fields
     * @param $order
     * @param $limit
     * @param $offset
     * @return array
     */
    private function getGoodsListParams($where, $fields, $order = '', $limit = 20, $offset = 0)
    {

        $newFields =[];
        foreach ($fields as $v){
            if(in_array($v,['likes','shareNum','businessId','depotId','depotUserId'])){
                continue;
            }
            $newFields[] = $v;
        }
        $this->setIsBuyLimitFields($newFields);
        if (isset($where['status'])) {
            $where['goodsStatus'] = $where['status'];
            unset($where['status']);
        }
        $params["where"] = json_encode($where);
        $params["fields"] = $newFields;
        $params["order"] = $order;
        $params["limit"] = $limit;
        $params["offset"] = $offset;
        return $params;
    }

    /**
     * 废弃
     * @param $where
     * @param $fields
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     * @deprecated
     */
    public function getGoodsListToArray($where, $fields, $order = '', $limit = 20, $offset = 0)
    {
        $field = $fields;
        $params = $this->getGoodsListParams($where, $fields, $order, $limit, $offset);
        if ($error = $this->validate($params, ['where' => 'required', 'fields' => 'required|array|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_GOODS_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        if ($ret['code'] == 0 && !empty($ret['data'])) {
            if (in_array('isBuyLimit', $field)) {
                foreach ($ret['data'] as $k => $val) {
                    $ret['data'][$k]['isBuyLimit'] = $val['buyLimit'] > 0 ? 1 : 0;
                    $ret['data'][$k]['buyLimit'] = $val['buyLimit'] == 0 ? 1 : $val['buyLimit'];
                }
            }
        }
        return $ret;
    }

    /**
     * 获取拍品列表
     * @param $where
     * @param $fields
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getGoodsList($where, $fields, $order = '', $limit = 20, $offset = 0)
    {
        $field = $fields;
        $params = $this->getGoodsListParams($where, $fields, $order, $limit, $offset);
        if ($error = $this->validate($params, ['where' => 'required', 'fields' => 'required|array|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_GOODS_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 0);
            return $data;
        });
        $this->setIsBuyLimitData($ret, $field);
        return $ret;
    }

    /**
     * @param $uri
     * @return array
     */
    public function uri2Id($uri)
    {
        $params["uri"] = $uri;
        if ($error = $this->validate($params, ['uri' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::GOODS_URI_2_ID, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param $where
     * @return array
     */
    public function getCount($where)
    {
        $params["where"] = json_encode($where);
        if ($error = $this->validate($params, ['where' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_GOODS_COUNT, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 1);
            return $data;
        });
        return $ret;
    }

    /**
     * @param array $ids
     * @param array $uris
     * @param array $fields
     * @return array|void
     */
    private function multiGetGoods(array $ids, array $uris, array $fields)
    {
        $field = $fields;
        $this->setIsBuyLimitFields($fields);
        $params["id"] = $ids;
        $params["uri"] = $uris;
        $params["fields"] = $fields;
        if ($error = $this->validate($params, ['fields' => 'required'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::MULTI_GET_BY_IDS, $params);
        $this->dealResultData($ret, function ($data) {
            $data = json_decode($data, 0);
            return $data;
        });

        $this->setIsBuyLimitData($ret, $field);
        return $ret;
    }

    /**
     * @param array $ids
     * @param array $fields
     * @return array|void
     */
    public function multiGetGoodsByIds(array $ids, array $fields)
    {
        return $this->multiGetGoods($ids, [], $fields);
    }

    /**
     * @param $fields
     */

    private function setIsBuyLimitFields(&$fields)
    {
        if (in_array('isBuyLimit', $fields)) {
            if (!in_array('buyLimit', $fields)) {
                array_push($fields, 'buyLimit');
            }
            $index = array_search('isBuyLimit', $fields);
            unset($fields[$index]);
            $fields = array_values($fields);
        }
    }

    /**
     * @param $ret
     * @param $fields
     */

    private function setIsBuyLimitData(&$ret, $fields)
    {

        if ($ret['code'] == 0 && !empty($ret['data'])) {
            if (in_array('isBuyLimit', $fields)) {
                foreach ($ret['data'] as $k => $val) {
                    $ret['data'][$k]->isBuyLimit = $val->buyLimit > 0 ? 1 : 0;
                    $ret['data'][$k]->buyLimit = $val->buyLimit == 0 ? 1 : $val->buyLimit;
                }
            }
        }
    }
}
