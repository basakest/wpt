<?php

namespace WptBus\Service\Sale\Module;

use WptBus\Service\BaseService;
use WptBus\Service\Sale\Router;
use WptBus\Model\Sale\Discovery\GetDiscoveryCommon;
use WptBus\Model\Sale\Discovery\GetDiscoveryData;
use WptBus\Model\Sale\Discovery\GetDiscoveryDataNoLogin;
use WptBus\Model\Sale\Discovery\GetDiscoveryGuess;

class Discovery extends BaseService
{
    /**
     *  获取逛逛数据
     * @param GetDiscoveryData $dto
     * @return array
     */
    public function getDiscoveryData(GetDiscoveryData $dto)
    {
        $data = [];
        $data['HideCategory'] = $dto->getHideCategory();
        $data['PlatForm'] = $dto->getPlatForm();
        $data['Page'] = $dto->getPage();
        $data['UserID'] = $dto->getUserId();
        $data['UserUri'] = $dto->getUserUri();
        $data['Num'] = $dto->getNum();
        $data['IP'] = $dto->getIp();
        $data['Screen'] = $dto->getScreen();
        $data['Columns'] = $dto->getColumns();
        $data['IsNoviceBuyer'] = $dto->getIsNoviceBuyer();
        $data['IsNewUser'] = $dto->getIsNewUser();
        $data['RegisterTime'] = $dto->getRegisterTime();
        $data['ExtraData'] = $dto->getExtraData();

        if (empty($data['Columns']) || empty($data['UserID']) || empty($data['UserUri'])) {
            return ['code' => 100, 'msg' => '参数错误'];
        }
        $data['Scene'] = 'index';
        $result = $this->httpPost(Router::DISCOVERY_GET_DATA, $data);
        if ($result['code'] <= 201000 && $result['code'] != 0) {
            $result = $this->httpPost(Router::DISCOVERY_GET_DATA_GUARANTEED_RECOMMEND_TOGETHER, $data);
        }
        if (empty($result['data'])) {
            return ['code' => 500, 'msg' => '网络错误'];
        }
        $ret = json_decode($result['data'], true);
        return $ret;
    }

    /**
     * 获取逛逛数据（未登录）
     * @param GetDiscoveryDataNoLogin $dto
     * @return array
     */
    public function getDiscoveryDataNoLogin(GetDiscoveryDataNoLogin $dto)
    {
        $data = [];
        $data['Num'] = $dto->getNum();
        $data['IP'] = $dto->getIp();
        $data['NoLoginUri'] = $dto->getNoLoginUri();
        $data['HideCategory'] = $dto->getHideCategory();
        $data['Direct'] = $dto->getDirect();
        $data['WptSceneChannel'] = $dto->getSc();
        $data['Os'] = $dto->getOs();
        $data['Ch'] = $dto->getCh();
        $data['Columns'] = $dto->getColumns();
        $data['Scene'] = 'index_nologin';
        $data['Page'] = $dto->getPage();
        $data['ExtraData'] = $dto->getExtraData();

        if (empty($data['NoLoginUri'])) {
            return ['code' => 100, 'msg' => '参数错误'];
        }

        $result = $this->httpPost(Router::DISCOVERY_GET_DATA, $data);

        if (empty($result['data'])) {
            return ['code' => 500, 'msg' => '网络错误'];
        }
        $ret = json_decode($result['data'], true);
        return $ret;
    }

    /**
     * 通用小场景接口
     * @param GetDiscoveryCommon $dto
     * @return array
     */
    public function getDiscoveryCommon(GetDiscoveryCommon $dto)
    {
        $data = [];
        $data['Type'] = $dto->getType();
        $data['HideCategory'] = $dto->getHideCategory();
        $data['UserUri'] = $dto->getUserUri();
        $data['Num'] = $dto->getNum();
        $data['IP'] = $dto->getIp();
        $data['ShowQingzhu'] = $dto->isShowQingzhu();
        $data['QingzhuNum'] = $dto->getQingzhuNum();
        $data['QingzhuType'] = $dto->getQingzhuType();
        $data['Columns'] = $dto->getColumns();
        $data['Scene'] = 'common';

        if (empty($data['Type'])) {
            return ['code' => 100, 'msg' => '参数错误'];
        }

        if ($data['ShowQingzhu'] && (empty($data['QingzhuNum']) || empty($data['QingzhuType']))) {
            return ['code' => 100, 'msg' => '参数错误'];
        }

        $result = $this->httpPost(Router::DISCOVERY_GET_DATA, $data);
        if (empty($result['data'])) {
            return ['code' => 500, 'msg' => '网络错误'];
        }
        $ret = json_decode($result['data'], true);
        return $ret;
    }

    /**
     * 404页面、店铺内无拍品时推荐
     * @param GetDiscoveryGuess $dto
     * @return mixed
     */
    public function getDiscoveryGuess(GetDiscoveryGuess $dto)
    {
        $data = [];
        $data['Num'] = $dto->getNum();
        $data['HideCategory'] = $dto->getHideCategory();
        $data['UserUri'] = $dto->getUserUri();
        $data['IP'] = $dto->getIp();
        $data['Screen'] = $dto->getScreen();
        $data['Columns'] = $dto->getColumns();
        $data['Scene'] = 'guess';

        $result = $this->httpPost(Router::DISCOVERY_GET_DATA, $data);
        if (empty($result['data'])) {
            return ['code' => 500, 'msg' => '网络错误'];
        }
        $ret = json_decode($result['data'], true);
        return $ret;
    }
}
