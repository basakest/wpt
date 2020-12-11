<?php


namespace WptBus\Service\Sale\Module;

use Closure;
use WptBus\Service\BaseService;
use WptBus\Service\Sale\Router;

class SaleComponent extends BaseService
{

    /**
     * @var array
     */
    protected $uris = [];

    /**
     * @var array
     */
    protected $ids = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @param array $uris
     * @return SaleComponent
     */
    public function setUris(array $uris): SaleComponent
    {
        $this->uris = $uris;
        return $this;
    }

    /**
     * @param array $ids
     * @return SaleComponent
     */
    public function setIds(array $ids): SaleComponent
    {
        $this->ids = $ids;
        return $this;
    }

    /**
     * @param array $fields
     * @return SaleComponent
     */
    public function setFields(array $fields): SaleComponent
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @return array
     */
    public function get()
    {
        if (empty($this->fields) || in_array("*", $this->fields)) {
            return [];
        }
        $saleListByIds = [];
        if ($this->ids) {
            $saleListByIds = $this->getSaleListByIds($this->ids, $this->fields);
        }
        $saleListByUris = [];
        if ($this->uris) {
            $saleListByUris = $this->getSaleListByUris($this->uris, $this->fields);
        }

        return array_merge($saleListByIds, $saleListByUris);
    }

    /**
     * @param $ids
     * @param $fields
     * @return array
     */
    protected function getSaleListByIds($ids, $fields)
    {
        $requestData = [];

        foreach ($ids as $id) {
            $requestData['ids'][] = (int)$id;
        }
        foreach ($fields as $field) {
            $requestData['fields'][] = (string)$field;
        }
        $result = $this->httpPost(Router::GET_SALE_COMPONENT_LIST_BY_IDS, $requestData);
        $this->dealResultData($result, $this->getAnalysisDataClosure());

        return $result;
    }

    /**
     * @param array $uris
     * @param array $fields
     * @return array
     */
    protected function getSaleListByUris(array $uris, array $fields)
    {
        $requestData = [];

        foreach ($uris as $uri) {
            $requestData['uris'][] = (string)$uri;
        }
        foreach ($fields as $field) {
            $requestData['fields'][] = (string)$field;
        }
        $result = $this->httpPost(Router::GET_SALE_COMPONENT_LIST_BY_URIS, $requestData);
        $this->dealResultData($result, $this->getAnalysisDataClosure());

        return $result;
    }

    /**
     * @return Closure
     */
    protected function getAnalysisDataClosure()
    {
        return function ($data) {
            if (!empty($data) && is_string($data)) {
                return json_decode($data, true);
            }
            return [];
        };
    }
}