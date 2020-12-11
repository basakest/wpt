<?php

namespace WptBus\Service\Sale\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Sale\Router;

class Normal extends BaseService
{
    /**
     * 将proto中json字符串的字段解析出来
     * @param array $data
     * @return array
     */
    protected function format(array $data)
    {
        if (!empty($data["categoryList"]) && is_string($data["categoryList"])) {
            $data["categoryList"] = json_decode($data['categoryList'], true);
        }
        if (!empty($data["templateList"]) && is_string($data["templateList"])) {
            $data["templateList"] = json_decode($data['templateList'], true);
        }
        if (!empty($data["withChainCodes"]) && is_string($data["withChainCodes"])) {
            $data["withChainCodes"] = json_decode($data['withChainCodes']);
        }
        return $data;
    }

    /**
     * 普通发拍 获取草稿箱
     * @param array $param
     * @return array|mixed
     */
    public function getDraft(array $param)
    {
        if (!isset($param['goodsId']) || !is_numeric($param['goodsId'])) {
            $param['goodsId'] = 0;
        }
        $draftId = (int)$param['goodsId'];
        $source = $param['source'] ?? 0;

        $result = $this->httpPost(
            Router::NORMAL_GET_DRAFT,
            [
                'draftId' => $draftId,
                'depotPrId' => $param['depotPrId'],
                'depotUserId' => $param['depotUserId'],
                'source' => (int)$source
            ],
            [
                'loginuserid' => intval($param['loginUserId'] ?? 0),
            ]
        );

        $result["data"] = $this->format($result["data"] ?? []);
        return $result;
    }

    /**
     * 产品库批量发拍
     * @param array $depotPrIds
     * @param int $depotUserId
     * @return array
     */
    public function multiGoodsPublish(int $userInfoId, array $depotPrIds, $depotUserId = 0)
    {
        $ids = [];
        foreach ($depotPrIds as $id) {
            $ids[] = (int)$id;
        }
        $data = [
            "depotPrIds" => $ids,
            "depotUserId" => (int)$depotUserId
        ];
        return $this->httpPost(
            Router::BATCH_PUBLISH_GOODS,
            $data,
            [
                'loginuserid' => $userInfoId,
            ]
        );
    }

    /**
     * 产品库批量发拍
     * @param array $goodsIds
     * @param array $param
     * @return array
     */
    public function multiSalePublish(array $goodsIds, array $param)
    {
        $data = [];

        $ids = [];
        foreach ($goodsIds as $id) {
            $ids[] = (int)$id;
        }

        $data['draftIds'] = $ids;
        if (!empty($param['endTime'])) {
            $data['endTime'] = (int)$param['endTime'];
        }
        if (!empty($param['enableReturn'])) {
            $data['enableReturn'] = (int)$param['enableReturn'];
        }
        if (!empty($param['gbImg'])) {
            $data['gbImg'] = (string)$param['gbImg'];
        }
        if (!empty($param['gbCode'])) {
            $data['gbCode'] = (string)$param['gbCode'];
        }
        if (!empty($param['identifyType'])) {
            $data['identifyType'] = (int)$param['identifyType'];
        }
        if (!empty($param['agreeSettle'])) {
            $data['agreeSettle'] = (int)$param['agreeSettle'];
        }
        if (!empty($param['bidBzj'])) {
            $data['bidBzj'] = (int)$param['bidBzj'];
        }
        if (!empty($param['bidbzjLimit'])) {
            $data['bidbzjLimit'] = (int)$param['bidbzjLimit'];
        }
        if (!empty($param['autoPayBzj'])) {
            $data['autoPayBzj'] = (bool)$param['autoPayBzj'];
        }
        if (!empty($param['tagId'])) {
            $data['tagId'] = (string)$param['tagId'];
        }
        if (!empty($param['bidMoney'])) {
            $data['bidMoney'] = (int)$param['bidMoney'];
        }
        if (!empty($param['increase'])) {
            $data['increase'] = (int)$param['increase'];
        }
        if (!empty($param['fixedPrice'])) {
            $data['fixedPrice'] = (int)$param['fixedPrice'];
        }
        if (!empty($param['quickEnd'])) {
            $data['quickEnd'] = (int)$param['quickEnd'];
        }
        if (!empty($param['expressFee'])) {
            $data['expressFee'] = (string)$param['expressFee'];
        }
        if (!empty($param['category'])) {
            $data['category'] = (int)$param['category'];
        }
        if (!empty($param['secCategory'])) {
            $data['secCategory'] = (int)$param['secCategory'];
        }
        if (!empty($param['referencePrice'])) {
            $data['referencePrice'] = (int)$param['referencePrice'];
        }
        if (!empty($param['multiWins'])) {
            $data['multiWins'] = (int)$param['multiWins'];
        }
        if (!empty($param['openTime'])) {
            $data['openTime'] = (int)$param['openTime'];
        }
        if (!empty($param['certchainCode'])) {
            $data['certChainCode'] = (string)$param['certchainCode'];
        }
        if (!empty($param['securityNum'])) {
            $data['securityNum'] = (string)$param['securityNum'];
        }
        if (!empty($param['isLiveSale'])) {
            $data['isLiveSale'] = (int)$param['isLiveSale'];
        }
        $data['delayTime'] = -1;
        if (isset($param['delayTime'])) {
            $data['delayTime'] = (int)$param['delayTime'];
        }
        if (!empty($param['scene'])) {
            $data['scene'] = $param['scene'];
        }
        return $this->httpPost(
            Router::BATCH_PUBLISH_SALE,
            $data,
            [
                'loginuserid' => intval($param['loginUserId'] ?? 0),
            ]
        );
    }

    /**
     * 普通发拍 插入草稿箱
     * @param array $param
     * @return array|mixed
     * @throws \Exception
     */
    public function insertDraft(array $param)
    {
        $data = [];
        if (!empty($param['title'])) {
            $data['title'] = $param['title'];
        }
        if (!empty($param['content'])) {
            $data['content'] = $param['content'];
        }
        if (!empty($param['goodsId'])) {
            $data['draftId'] = (int)$param['goodsId'];
        }
        if (!empty($param['imgList'])) {
            $data['imgList'] = $param['imgList'];
        }
        if (!empty($param['video'])) {
            $data['video'] = $param['video'];
        }
        if (!empty($param['secCategoryTemplate'])) {
            $data['secCategoryTemplate'] = $param['secCategoryTemplate'];
        }
        if (!empty($param['secCategory'])) {
            $data['secCategory'] = (int)$param['secCategory'];
        }
        if (!empty($param['category'])) {
            $data['category'] = (int)$param['category'];
        }
        if (!empty($param['tagId'])) {
            $data['tagId'] = $param['tagId'];
        }
        if (!empty($param['type'])) {
            $data['type'] = $param['type'];
        }
        if (!empty($param['isDraft'])) {
            $data['isDraft'] = (int)$param['isDraft'];
        }
        if (!empty($param['pdId'])) {
            $data['pdId'] = (int)$param['pdId'];
        }
        if (!empty($param['depotPrId'])) {
            $data['depotPrId'] = (int)$param['depotPrId'];
        }
        if (!empty($param['depotUserId'])) {
            $data['depotUserId'] = (int)$param['depotUserId'];
        }
        if (!empty($param['withChainCodes'])) {
            $data['withChainCodes'] = json_decode($param['withChainCodes'], true);
        }
        if (!empty($param['appSource'])) {
            $data['appSource'] = $param['appSource'];
        }
        if (!empty($param['masterId'])) {
            $data['masterId'] = (int)$param['masterId'];
        }
        if (!empty($param['identCertImgList'])) {
            $data['identCertImgList'] = $param['identCertImgList'];
        }
        if (!empty($param['liquorYear'])) {
            $data['liquorYear'] = $param['liquorYear'];
        }
        if (!empty($param['liquorAuthCertImgList'])) {
            $data['liquorAuthCertImgList'] = $param['liquorAuthCertImgList'];
        }

        return $this->httpPost(
            Router::NORMAL_INSERT_DRAFT,
            $data,
            [
                'loginuserid' => intval($param['loginUserId'] ?? 0),
            ]
        );
    }

    /**
     * 普通拍品发拍验证
     * @param array $param
     * @return array|mixed
     */
    public function getSale(array $param)
    {
        $data = [];
        if (!empty($param["goodsId"])) {
            $data["draftId"] = (int)$param["goodsId"];
        }
        if (!empty($param["isWebApp"])) {
            $data['isWebApp'] = (int)$param['isWebApp'];
        }
        if (!empty($param["category"])) {
            $data['category'] = (int)$param['category'];
        }
        if (!empty($param["secCategory"])) {
            $data['secCategory'] = (int)$param['secCategory'];
        }

        if (!empty($param["source"])) {
            $data['source'] = (int)$param['source'];
        }

        $result = $this->httpPost(
            Router::NORMAL_GET_SALE,
            $data,
            [
                'loginuserid' => intval($param['loginUserId'] ?? 0),
            ]
        );

        $result["data"] = $this->format($result["data"] ?? []);
        return $result;
    }

    /**
     * 普通发拍 插入sale
     * @param array $param
     * @return array|mixed
     */
    public function insertSale(array $param)
    {
        $data = [];
        if (!empty($param['goodsId'])) {
            $data['draftId'] = (int)$param['goodsId'];
        }
        if (!empty($param['endTime'])) {
            $data['endTime'] = (int)$param['endTime'];
        }
        if (!empty($param['enableReturn'])) {
            $data['enableReturn'] = (int)$param['enableReturn'];
        }
        if (!empty($param['gbImg'])) {
            $data['gbImg'] = (string)$param['gbImg'];
        }
        if (!empty($param['gbCode'])) {
            $data['gbCode'] = (string)$param['gbCode'];
        }
        if (!empty($param['identifyType'])) {
            $data['identifyType'] = (int)$param['identifyType'];
        }
        if (!empty($param['agreeSettle'])) {
            $data['agreeSettle'] = (int)$param['agreeSettle'];
        }
        if (!empty($param['bidBzj'])) {
            $data['bidBzj'] = (int)$param['bidBzj'];
        }
        if (!empty($param['bidbzjLimit'])) {
            $data['bidbzjLimit'] = (int)$param['bidbzjLimit'];
        }
        if (!empty($param['autoPayBzj'])) {
            $data['autoPayBzj'] = (bool)$param['autoPayBzj'];
        }
        if (!empty($param['tagId'])) {
            $data['tagId'] = (string)$param['tagId'];
        }
        if (!empty($param['bidMoney'])) {
            $data['bidMoney'] = (int)$param['bidMoney'];
        }
        if (!empty($param['increase'])) {
            $data['increase'] = (int)$param['increase'];
        }
        if (!empty($param['fixedPrice'])) {
            $data['fixedPrice'] = (int)$param['fixedPrice'];
        }
        if (!empty($param['quickEnd'])) {
            $data['quickEnd'] = (int)$param['quickEnd'];
        }
        if (!empty($param['expressFee'])) {
            $data['expressFee'] = (string)$param['expressFee'];
        }
        if (!empty($param['category'])) {
            $data['category'] = (int)$param['category'];
        }
        if (!empty($param['secCategory'])) {
            $data['secCategory'] = (int)$param['secCategory'];
        }
        if (!empty($param['referencePrice'])) {
            $data['referencePrice'] = (int)$param['referencePrice'];
        }
        if (!empty($param['multiWins'])) {
            $data['multiWins'] = (int)$param['multiWins'];
        }
        if (!empty($param['openTime'])) {
            $data['openTime'] = (int)$param['openTime'];
        }
        if (!empty($param['certchainCode'])) {
            $data['certChainCode'] = (string)$param['certchainCode'];
        }
        if (!empty($param['securityNum'])) {
            $data['securityNum'] = (string)$param['securityNum'];
        }
        if (!empty($param['isLiveSale'])) {
            $data['isLiveSale'] = (int)$param['isLiveSale'];
        }
        $data['delayTime'] = -1;
        if (isset($param['delayTime'])) {
            $data['delayTime'] = (int)$param['delayTime'];
        }

        if (!empty($param['auctionCommRate'])) {
            $data['auctionCommRate'] = (int)$param['auctionCommRate'];
        }
        if (!empty($param['scene'])) {
            $data['scene'] = $param['scene'];
        }

        if (!empty($param["isRecommendMasterSale"])) {
            $data["isRecommendMasterSale"] = (bool)$param["isRecommendMasterSale"];
        }

        if (!empty($param['expressFeeTemplateId'])) {
            $data['expressFeeTemplateId'] = intval($param['expressFeeTemplateId']);
        }

        if (!empty($param['expressFeeAttributie'])) {
            $data['expressFeeAttributie'] = strval($param['expressFeeAttributie']);
        }

        return $this->httpPost(
            Router::NORMAL_INSERT_SALE,
            $data,
            [
                'loginuserid' => intval($param['loginUserId'] ?? 0),
            ]
        );
    }

    /**
     * 重新发拍
     * @param string $saleUri
     * @return array
     */
    public function saleRepublish(int $loginUserId, string $saleUri)
    {
        return $this->httpPost(
            Router::NORMAL_SALE_REPUBLISH,
            ['saleUri' => $saleUri],
            [
                'loginuserid' => $loginUserId,
            ]
        );
    }

    /**
     * 直播间快速发拍
     * @param array $param
     * @return array
     */
    public function quickSalePublish(array $param)
    {
        $data = [];
        if (!empty($param['title'])) {
            $data['title'] = $param['title'];
        }
        if (!empty($param['content'])) {
            $data['content'] = $param['content'];
        }
        if (!empty($param['imgList'])) {
            if (is_string($param['imgList'])) {
                $param['imgList'] = json_decode($param['imgList'], true);
            }
            $data['imgList'] = $param['imgList'];
        }
        if (!empty($param['secCategory'])) {
            $data['secCategory'] = (int)$param['secCategory'];
        }
        if (!empty($param['category'])) {
            $data['category'] = (int)$param['category'];
        }
        if (!empty($param['type'])) {
            $data['type'] = $param['type'];
        }
        if (!empty($param['isDraft'])) {
            $data['isDraft'] = (int)$param['isDraft'];
        }
        if (!empty($param['appSource'])) {
            $data['appSource'] = $param['appSource'];
        }
        if (!empty($param['endTime'])) {
            $data['endTime'] = (int)$param['endTime'];
        }
        if (!empty($param['enableReturn'])) {
            $data['enableReturn'] = (int)$param['enableReturn'];
        }
        if (!empty($param['agreeSettle'])) {
            $data['agreeSettle'] = (int)$param['agreeSettle'];
        }
        if (!empty($param['bidBzj'])) {
            $data['bidBzj'] = (int)$param['bidBzj'];
        }
        if (!empty($param['bidbzjLimit'])) {
            $data['bidbzjLimit'] = (int)$param['bidbzjLimit'];
        }
        if (!empty($param['autoPayBzj'])) {
            $data['autoPayBzj'] = (bool)$param['autoPayBzj'];
        }
        if (!empty($param['bidMoney'])) {
            $data['bidMoney'] = (int)$param['bidMoney'];
        }
        if (!empty($param['increase'])) {
            $data['increase'] = (int)$param['increase'];
        }
        if (!empty($param['fixedPrice'])) {
            $data['fixedPrice'] = (int)$param['fixedPrice'];
        }
        if (!empty($param['quickEnd'])) {
            $data['quickEnd'] = (int)$param['quickEnd'];
        }
        if (!empty($param['expressFee'])) {
            $data['expressFee'] = (string)$param['expressFee'];
        }
        if (!empty($param['referencePrice'])) {
            $data['referencePrice'] = (int)$param['referencePrice'];
        }
        if (!empty($param['multiWins'])) {
            $data['multiWins'] = (int)$param['multiWins'];
        }
        if (!empty($param['openTime'])) {
            $data['openTime'] = (int)$param['openTime'];
        }
        if (!empty($param['isLiveSale'])) {
            $data['isLiveSale'] = (int)$param['isLiveSale'];
        }
        $data['delayTime'] = -1;
        if (isset($param['delayTime'])) {
            $data['delayTime'] = (int)$param['delayTime'];
        }
        $data['commissionRate'] = -1;
        if (isset($param['commissionRate'])) {
            $data['commissionRate'] = (int) $param['commissionRate'];
        }

        if (!empty($param['expressFeeTemplateId'])) {
            $data['expressFeeTemplateId'] = intval($param['expressFeeTemplateId']);
        }

        if (!empty($param['expressFeeAttributie'])) {
            $data['expressFeeAttributie'] = strval($param['expressFeeAttributie']);
        }

        if (isset($param['unProcessed'])) {
            $data['unProcessed'] = (int) $param['unProcessed'];
        }

        return $this->httpPost(
            Router::LIVE_SALE_QUICK_PUBLISH,
            $data,
            [
                'loginuserid' => intval($param['loginUserId'] ?? 0),
            ]
        );
    }
}
