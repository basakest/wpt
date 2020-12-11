<?php


namespace WptBus\Service\Order\Module;

use App\Utils\CommonUtil;
use WptBus\Service\BaseService;
use WptBus\Service\Order\Router;

class WorkRate extends BaseService
{
    public function getWorkRateAmendLog($saleId)
    {
        $params = ["saleId" => (int)$saleId];
        if ($error = $this->validate($params, ['saleId' => 'required|integer|min:1'])) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_WORK_RATE_AMEND_LOG, $params);
        $this->dealResultData($ret, function ($data) {
            return json_decode($data, true);
        });
        return $ret;
    }

    public function getWorkRateDetail($shopId, $saleId)
    {
        $params = [
            "shopId" => (int)$shopId,
            "saleId" => (int)$saleId
        ];
        $error = $this->validate($params, ['shopId' => 'required|integer|min:1', 'saleId' => 'required|integer|min:1']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_WORK_RATE_DETAIL, $params);
        $this->dealResultData($ret, function ($data) {
            $info = json_decode($data, true);
            $info['media'] = $this->getMedia($info['mediaJson'], 'id');
            $info['appendMedia'] = $this->getMedia($info['appendMediaJson'], 'id');
            $info['tags'] = json_decode($info['tagJson'], true);
            $info['state'] = $info['appealStatus'];
            unset($info['mediaJson'], $info['appendMediaJson'], $info['tagJson'], $info['appealStatus']);
            return $info;
        });
        return $ret;
    }

    public function getWorkRateList($params)
    {
        $error = $this->validate($params, ['shopId' => 'required_without:sellerPhone', 'sellerPhone' => 'required_without:shopId']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_WORK_RATE_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $list = $data ? json_decode($data, true) : [];
            return $list;
        });
        return $ret;
    }

    public function saveRate($shopId, $id, $update)
    {
        $params = ['shopId' => $shopId, 'id' => $id];
        $error = $this->validate($params, ['shopId' => 'required', 'id' => 'required']);
        if ($error) {
            return $error;
        }

        if (isset($update['isDiscard'])) {
            $update['isDiscard'] = (int)$update['isDiscard'];
        }
        if (isset($update['shield'])) {
            $update['shield'] = (int)$update['shield'];
        }

        $params['data'] = json_encode($update);
        $ret = $this->httpPost(Router::SAVE_WORK_RATE, $params);
        $this->dealResultData($ret, function ($data) {
            $list = $data ? json_decode($data, true) : [];
            return $list;
        });
        return $ret;
    }

    /**
     * 给后台用的 批量获取评论信息
     * @param int $shopId
     * @param array $saleId
     * @return array|void
     */
    public function batchGetSaleRate(int $shopId, array $saleId)
    {
        $params = [
            "shopId" => (int)$shopId,
            "saleId" => (array)$saleId
        ];
        $error = $this->validate($params, ['shopId' => 'required|integer|min:1', 'saleId' => 'required']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::BATCH_GET_SALE_RATE, $params);
        $this->dealResultData($ret, function ($data) {
            return $data;
        });
        return $ret;
    }

    /**
     * 给工单后台用的 修改申诉状态
     * @param $shopId
     * @param $saleId
     * @param $status
     * @return array|void
     */
    public function setRateAppealStatus($shopId, $saleId, $status)
    {
        $params = ['shopId' => $shopId, 'saleId' => $saleId, 'status' => $status];
        $error = $this->validate($params, ['shopId' => 'required', 'saleId' => 'required', 'status' => 'required']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::SET_WORK_RATE_APPEAL_STAUTS, $params);
        $this->dealResultData($ret, function ($data) {
            $list = $data ? json_decode($data, true) : [];
            return $list;
        });
        return $ret;
    }

    /**
     * 取评价图片
     * @param $media
     * @param string $return
     * @return array
     */
    private function getMedia($media, $return = '')
    {
        $imgArr = get_property($media, 'imgs', []);
        if (empty($imgArr)) {
            return [];
        }

        $mediaList = [];
        foreach ($imgArr as $key => $img) {
            if ($key >= 6) {
                break;
            }
            if ($return == 'id') {
                $mediaList[] = [
                    'id' => $img,
                    'url' => CommonUtil::combineImgUrl($img, 640)
                ];
            } else {
                $mediaList[] = CommonUtil::combineImgUrl($img, 640);
            }
        }
        return ['imgs' => $mediaList];
    }


    public function autoRate($saleId)
    {
        $params = ['saleId' => $saleId];
        $error = $this->validate($params, ['saleId' => 'required']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::AUTO_RATE, $params);
        $this->dealResultData($ret, function ($data) {
            return $data == "true";
        });
        return $ret;
    }
}
