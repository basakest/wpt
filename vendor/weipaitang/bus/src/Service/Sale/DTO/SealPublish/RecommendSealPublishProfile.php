<?php


namespace WptBus\Service\Sale\DTO\SealPublish;


class RecommendSealPublishProfile implements SealPublish, \JsonSerializable
{
    private $imgs;
    private $sealType;
    private $sealCate;
    private $cert;
    private $content;
    private $userinfoId;
    private $recommendPromotion;
    private $mScene = 'recommendSeal';

    public function getMScene()
    {
        return $this->mScene;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $ret = [];
        $varList = get_object_vars($this);
        foreach ($varList as $key => $val) {
            if (isset($this->$key) && $key != "mScene") {
                $ret[$key] = $val;
            }
        }

        return $ret;
    }

    /**
     * @return mixed
     */
    public function getImgs()
    {
        return $this->imgs;
    }

    /**
     * @param mixed $imgs
     */
    public function setImgs($imgs)
    {
        $this->imgs = $imgs;
    }

    /**
     * @return mixed
     */
    public function getSealType()
    {
        return $this->sealType;
    }

    /**
     * @param mixed $sealType
     */
    public function setSealType($sealType)
    {
        $this->sealType = $sealType;
    }

    /**
     * @return mixed
     */
    public function getSealCate()
    {
        return $this->sealCate;
    }

    /**
     * @param mixed $sealCate
     */
    public function setSealCate($sealCate)
    {
        $this->sealCate = $sealCate;
    }

    /**
     * @return mixed
     */
    public function getCert()
    {
        return $this->cert;
    }

    /**
     * @param mixed $cert
     */
    public function setCert($cert)
    {
        $this->cert = $cert;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getUserinfoId()
    {
        return $this->userinfoId;
    }

    /**
     * @param mixed $userinfoId
     */
    public function setUserinfoId($userinfoId)
    {
        $this->userinfoId = $userinfoId;
    }

    /**
     * @return mixed
     */
    public function getRecommendPromotion()
    {
        return $this->recommendPromotion;
    }

    /**
     * @param mixed $recommendPromotion
     */
    public function setRecommendPromotion($recommendPromotion)
    {
        $this->recommendPromotion = $recommendPromotion;
    }


}
