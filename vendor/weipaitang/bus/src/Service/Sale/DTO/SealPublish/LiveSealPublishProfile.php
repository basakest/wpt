<?php


namespace WptBus\Service\Sale\DTO\SealPublish;


class LiveSealPublishProfile implements SealPublish, \JsonSerializable
{
    private $imgs;
    private $sealType;
    private $content;
    private $userinfoId;
    private $startTime;
    private $endTime;
    private $livePromotion;
    private $mScene = 'liveSeal';

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
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return mixed
     */
    public function getLivePromotion()
    {
        return $this->livePromotion;
    }

    /**
     * @param mixed $livePromotion
     */
    public function setLivePromotion($livePromotion)
    {
        $this->livePromotion = $livePromotion;
    }

}
