<?php
namespace WptBus\Lib;

use App\Facades\ConfigCenter\NifflerConfig;

class WhiteList {

    private $masterKey = 'api.weipaitang.com';

    private $subKey = 'create-order-white-list';

    private $ratioKey = 'create-order-ratio';

    private $whiteList = [];

    public static function getInstance()
    {
        return new self();
    }


    public function __construct()
    {
        $ret = NifflerConfig::getConfig($this->masterKey, $this->subKey);
        if($ret->success && $ret->data){
            $this->whiteList = json_decode($ret->data,1);
        }
    }

    public function isWhiteList(int $userinfoId){
        return in_array($userinfoId,$this->whiteList);
    }

    public function isRatio() {
       return  DataCompare::getInstance()->isGray($this->ratioKey);
    }

    public function isCreate(int $userinfoId){

        if ($this->isWhiteList($userinfoId)){
            return true;
        }
        if ($this->isRatio()){
            return true;
        }
        return false;
    }
}