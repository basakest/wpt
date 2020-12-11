<?php

namespace WptBus\Service\Sale\Module;

use WptBus\Exception\BusException;
use WptBus\Service\BaseService;
use WptBus\Service\Sale\Router;

class Master extends BaseService
{
    public function arrayToObject($arr)
    {
        if (is_array($arr)) {
            return json_decode(json_encode($arr));
        } else {
            return $arr;
        }
    }

    public function getMasterSaleIdList(int $shopId)
    {
        $data = [];
        $data['UserInfoId'] = $shopId;

        $result = $this->httpPost(Router::MASTER_GET_MASTER_SALE_IDS, $data);

        if ($result['code'] != 0) {
            throw new BusException('网络错误', 100);
        }

        if (empty($result['data'])) {
            return [];
        }

        return $result['data'];
    }

    public function setTopMasterSale(int $saleId, bool $setTop, $topCover = '', $topCoverOrg = '')
    {
        $data = [];
        $data['SaleId'] = $saleId;
        $data['SetTop'] = $setTop;
        $data['TopCover'] = $topCover;
        $data['TopCoverOrg'] = $topCoverOrg;

        $result = $this->httpPost(Router::MASTER_SET_TOP_MASTER_SALE, $data);

        return $result;
    }

    public function getSaleListByMasterId(int $masterId, string $order, int $limit, int $offset)
    {
        $data = [];
        $data['MasterId'] = $masterId;
        $data['Order'] = $order;
        $data['Limit'] = $limit;
        $data['Offset'] = $offset;

        $result = $this->httpPost(Router::MASTER_GET_SALE_LIST_BY_MASTER_ID, $data);

        $res = $result['data'] ?? [];
        if (empty($res)) {
            return [];
        }

        $res = json_decode($res, true);
        $res = $this->arrayToObject($res);

        return $res;
    }

    public function getTopMasterSaleList()
    {
        $result = $this->httpPost(Router::MASTER_GET_TOP_MASTER_SALE_LIST);

        $res = $result['data'] ?? [];
        if (empty($res)) {
            return [];
        }

        $res = json_decode($res, true);
        $res = $this->arrayToObject($res);

        return $res;
    }

    public function getMasterSaleList(int $limit, int $offset, string $timeStamp)
    {
        $data = [];
        $data['Limit'] = $limit;
        $data['Offset'] = $offset;
        $data['TimeStamp'] = $timeStamp;

        $result = $this->httpPost(Router::MASTER_GET_MASTER_SALE_LIST, $data);

        $res = $result['data'] ?? [];
        if (empty($res)) {
            return [];
        }

        $res = $this->arrayToObject($res);

        if ($res->list) {
            $res->list = json_decode($res->list);
        }

        return $res;
    }

    public function setMasterSaleInfo(int $saleId, float $score, int $isValid)
    {
        $data = [];
        $data['SaleId'] = $saleId;
        $data['Score'] = $score;
        $data['IsValid'] = $isValid;

        $result = $this->httpPost(Router::MASTER_SET_MASTER_SALE_INFO, $data);

        return $result['data'] ?? false;
    }

    public function getPrevNextMasterSale(int $saleId, int $length, string $timeStamp)
    {
        $data = [];
        $data['SaleId'] = $saleId;
        $data['Length'] = $length;
        $data['TimeStamp'] = $timeStamp;

        $result = $this->httpPost(Router::MASTER_GET_PREV_NEXT_MASTER_SALE, $data);

        $res = $result['data'] ?? [];
        if (empty($res)) {
            return null;
        }

        $res = $this->arrayToObject($res);

        return $res;
    }

    public function searchMasterSales(
        $saleIds,
        int $saleId,
        int $userinfoId,
        int $masterId,
        $category,
        string $status,
        string $showStatus,
        string $order,
        int $limit,
        int $offset
    ) {
        $data = [];
        $data['SaleId'] = $saleId;
        $data['UserInfoId'] = $userinfoId;
        $data['MasterId'] = $masterId;
        $data['Status'] = $status;
        $data['ShowStatus'] = $showStatus;
        $data['Order'] = $order;
        $data['Limit'] = $limit;
        $data['Offset'] = $offset;
        $data['Category'] = (int) $category;

        if (!empty($saleIds)) {
            $data['SaleIds'] = $saleIds;
        }

        $result = $this->httpPost(Router::MASTER_SEARCH_MASTER_SALES, $data);

        $res = $result['data'] ?? [];
        if (empty($res)) {
            return null;
        }

        $res = $this->arrayToObject($res);

        return $res;
    }

    public function setMasterSaleList(string $timeStamp, array $masterSaleArr)
    {
        $data = [];
        $data['TimeStamp'] = $timeStamp;
        $data['MasterInfos'] = $masterSaleArr;

        $result = $this->httpPost(Router::MASTER_SET_MASTER_SALE_LIST, $data);

        return $result['data'] ?? false;
    }

    public function getMasterSaleInfo(string $saleUri)
    {
        $data = [];
        $data['SaleUri'] = $saleUri;

        $result = $this->httpPost(Router::MASTER_GET_MASTER_SALE_INFO, $data);

        $res = $result['data'] ?? [];
        if (empty($res)) {
            return null;
        }

        $res = json_decode($res);

        if (is_array($res)) {
            $res = $this->arrayToObject($res);
        }

        return $res;
    }

    public function searchAuditMasterSales($input)
    {
        $data = [];
        if (!empty($input['saleId'])) {
            $data['SaleId'] = (int)$input['saleId'];
        }
        if (!empty($input['uid'])) {
            $data['UserInfoId'] = (int)$input['uid'];
        }
        if (!empty($input['masterId'])) {
            $data['MasterId'] = (int)$input['masterId'];
        }
        if (!empty($input['category'])) {
            $data['Category'] = (int)$input['category'];
        }
        if (!empty($input['auditStatus'])) {
            $data['AuditStatus'] = (int)$input['auditStatus'];
        }
        if (!empty($input['minEndTime'])) {
            $data['MinEndTime'] = (int)$input['minEndTime'];
        }
        if (!empty($input['maxEndTime'])) {
            $data['MaxEndTime'] = (int)$input['maxEndTime'];
        }
        if (!empty($input['order'])) {
            $data['Order'] = $input['order'];
        }
        if (!empty($input['limit'])) {
            $data['Limit'] = (int)$input['limit'];
        }
        if (!empty($input['offset'])) {
            $data['Offset'] = (int)$input['offset'];
        }

        $result = $this->httpPost(Router::MASTER_SEARCH_MASTER_SALES, $data);

        $res = $result['data'] ?? [];
        if (empty($res)) {
            return null;
        }
        $res = $this->arrayToObject($res);

        return $res;
    }
}
