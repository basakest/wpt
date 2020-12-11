<?php

namespace WptBus\Service\Order\Module;

use App\ConstDir\IconConst;
use App\Utils\CommonUtil;
use http\Exception\RuntimeException;
use Monolog\Logger;
use WptBus\Lib\Error;
use WptBus\Lib\Log;
use WptBus\Lib\Response;
use WptBus\Lib\Utils;
use WptBus\Lib\Validator;
use WptBus\Service\Order\Router;
use WptUtils\Arr;

class SellerRate extends \WptBus\Service\BaseService
{
    public function getSellerRateList($userId, $isSystem, $appealStatus, $score, $offset, $limit)
    {
        $params["userinfoId"] = (int)$userId;
        $params["isSystem"] = (int)$isSystem;
        $params["score"] = (int)$score;
        $params["status"] = (int)$appealStatus;
        $params["limit"] = (int)$limit;
        $params["offset"] = (int)$offset;
        $error = $this->validate($params, ['userinfoId' => 'required', 'isSystem' => 'required', 'score' => 'required', 'status' => 'required', 'limit' => 'required', 'offset' => 'required']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::SELLE_RRATE_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $list = json_decode($data, 1);
            $lists = $list["data"];
            if ($lists) {
                foreach ((array)$lists as $key => $val) {
                    $lists[$key]["saleInfo"]["img"] = CommonUtil::combineImgUrl($val["saleInfo"]["img"], 96);
                    $lists[$key]["imgList"] = $this->getRateImgList(json_decode($val["imgList"], true));
                    $lists[$key]["appendImgList"] = $this->getRateImgList(json_decode($val["appendImgList"], true));
                    $lists[$key]["tags"] = json_decode($val["tags"], true) ?? [];
                    $lists[$key]["content"] = html_entity_decode($val["content"]);
                    $lists[$key]["appendContent"] = html_entity_decode($val["appendContent"]);
                }
            } else {
                $lists = [];
            }
            $out["list"] = $lists;
            $out["total"] = $list["total"];
            return $out;
        });
        return $ret;
    }

    public function getShopRateTagList($userId, $tagId, $offset, $limit)
    {
        $params["userinfoId"] = (int)$userId;
        $params["tagId"] = (int)$tagId;
        $params["limit"] = (int)$limit;
        $params["offset"] = (int)$offset;
        $error = $this->validate($params, ['userinfoId' => 'required', 'tagId' => 'required', 'limit' => 'required', 'offset' => 'required']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_SHOP_RATE_TAGS_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $lists = json_decode($data, 1);
            if ($lists) {
                foreach ((array)$lists as $key => $val) {
                    $lists[$key]["saleInfo"]["img"] = CommonUtil::combineImgUrl($val["saleInfo"]["img"], 96);
                    $lists[$key]["imgList"] = $this->getRateImgList(json_decode($val["imgList"], true));
                    $lists[$key]["appendImgList"] = $this->getRateImgList(json_decode($val["appendImgList"], true));
                    $lists[$key]["tags"] = json_decode($val["tags"], true) ?? [];
                    $lists[$key]["memberLevelUrl"] = IconConst::MEMBER_LEVEL_ICON[$lists[$key]['memberLevel']];
                    $lists[$key]["content"] = html_entity_decode($val["content"]);
                    $lists[$key]["createTimeDes"] = date("Y-m-d", $val["createTime"]);
                    if (isset($val["appendContent"])) {
                        $lists[$key]["appendContent"] = html_entity_decode($val["appendContent"]);
                    }
                }
            } else {
                $lists = [];
            }
            return $lists;
        });
        return $ret;
    }

    /**
     * @param int $shopId 店铺id
     * @param array $saleIds 拍品id列表
     * @return array|void
     */
    public function batchGetSellerRateListBySaleId(int $shopId, array $saleIds)
    {
        $params['shopId'] = $shopId;
        $params['saleId'] = $saleIds;
        $error = $this->validate($params, ['shopId' => 'required', 'saleId' => 'required|array|min:1']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::BATCH_GET_SELLER_RATE_LIST, $params);
        $this->dealResultData($ret, function ($data) {
            $lists = json_decode($data, 1);
            if ($lists) {
                foreach ((array)$lists as $key => $val) {
                    $lists[$key]["saleInfo"]["img"] = CommonUtil::combineImgUrl($val["saleInfo"]["img"], 96);
                    $lists[$key]["imgList"] = $this->getRateImgList(json_decode($val["imgList"], true));
                    $lists[$key]["appendImgList"] = $this->getRateImgList(json_decode($val["appendImgList"], true));
                    $lists[$key]["tags"] = json_decode($val["tags"], true) ?? [];
                    $lists[$key]["memberLevelUrl"] = IconConst::MEMBER_LEVEL_ICON[$lists[$key]['memberLevel']];
                    $lists[$key]["content"] = html_entity_decode($val["content"]);
                    $lists[$key]["createTimeDes"] = date("Y-m-d", $val["createTime"]);
                    if (isset($val["appendContent"])) {
                        $lists[$key]["appendContent"] = html_entity_decode($val["appendContent"]);
                    }
                }
            } else {
                $lists = [];
            }
            return $lists;
        });
        return $ret;
    }

    function getShopRateTagsCount(int $shopId, array $tagType)
    {
        $params["userinfoId"] = (int)$shopId;
        $params["tagType"] = (array)$tagType;
        $error = $this->validate($params, ['userinfoId' => 'required']);
        if ($error) {
            return $error;
        }

        $ret = $this->setTimeout(3000)->httpPost(Router::GET_SHOP_RATE_TAGS_COUNT, $params);
        Log::info($this->serviceName, "获取店铺标签2：getShopRateTagsCount", [$shopId, $tagType, $ret]);
        $this->dealResultData($ret, function ($data) {
            $list = json_decode($data, 1);
            if ($list) {
                foreach ($list as $k => &$v) {
                    if ($v["tagName"] == "最新") {
                        unset($v["Number"]);
                    }
                }
            }
            return Arr::multiSort($list, ['tagType' => SORT_DESC, 'tagId' => SORT_ASC]);
        });

        if ($ret['code'] != 0) {
            Log::error($this->serviceName, "获取店铺标签2：getShopRateTagsCount", [$ret['code'], $ret['msg']]);
        }
        Log::info($this->serviceName, "获取店铺标签2：getShopRateTagsCount", [$shopId, $tagType, $ret]);
        return $ret;
    }

    function getSellerDetail(int $shopId, string $saleUri)
    {
        $params["userinfoId"] = (int)$shopId;
        $params["saleUri"] = (string)$saleUri;
        $error = $this->validate($params, ['userinfoId' => 'required', 'saleUri' => 'required']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_SELLER_RATE_DETAIL, $params);
        $this->dealResultData($ret, function ($data) {
            $info = json_decode($data, 1);
            if ($info) {
                $info["saleInfo"]["img"] = CommonUtil::combineImgUrl($info["saleInfo"]["img"], 96);
                $info["imgList"] = $this->getRateImgList(json_decode($info["imgList"], true));
                $info["appendImgList"] = $this->getRateImgList(json_decode($info["appendImgList"], true));
                $info["tags"] = json_decode($info["tags"], true) ?? [];
                if (isset($info["content"])) {
                    $info["content"] = html_entity_decode($info["content"]);
                }
                if (isset($info["appendContent"])) {
                    $info["appendContent"] = html_entity_decode($info["appendContent"]);
                }
            }
            return $info;
        });
        return $ret;
    }

    function getRateInfo(int $shopId, string $saleUri)
    {
        $params["userinfoId"] = (int)$shopId;
        $params["saleUri"] = (string)$saleUri;
        $error = $this->validate($params, ['userinfoId' => 'required', 'saleUri' => 'required']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_SELLER_RATE_DETAIL, $params);
        $this->dealResultData($ret, function ($data) {
            $info = json_decode($data, 1);
            if ($info) {
                $info["content"] = $info["content"] == "该用户没有填写评价" ? "" : $info["content"];
            }
            return $info;
        });
        return $ret;
    }

    function getAppealDetail(int $shopId, string $saleUri)
    {
        $params["userinfoId"] = (int)$shopId;
        $params["saleUri"] = (string)$saleUri;
        $error = $this->validate($params, ['userinfoId' => 'required', 'saleUri' => 'required']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::GET_APPEAL_DETAIL, $params);
        $this->dealResultData($ret, function ($data) {
            $info = json_decode($data, 1);
            if ($info) {
                $info["imgs"] = $this->getRateImgList(json_decode($info["imgs"], true), 240, 6);
            }
            return $info;
        });
        return $ret;
    }

    function saveRateReply(int $shopId, string $saleUri, string $reply)
    {
        $params["userinfoId"] = (int)$shopId;
        $params["saleUri"] = (string)$saleUri;
        $params["reply"] = (string)$reply;
        $error = $this->validate($params, ['userinfoId' => 'required', 'saleUri' => 'required', 'reply' => 'required']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::SAVE_RATE_REPLY, $params);
        $this->dealResultData($ret, function ($data) {
            if ($data == 1) {
                return true;
            }
            return false;
        });
        return $ret;
    }

    function saveRateAppeal(int $shopId, string $saleUri, string $content, $reason, $imgs)
    {
        $imgList = [];
        $imgsArray = [];
        if ($imgs) {
            $imgsArray = json_decode($imgs, true);
            foreach ($imgsArray as $key => $img) {
                if ($key >= 6) {
                    break;
                }
                $imgList[] = CommonUtil::combineImgUrl($img, 640);
            }
            //校验图片是否合法
            foreach ($imgsArray as $img) {
                if (preg_match('#(http|weixin)#', $img)) {
                    Utils::throwException(100, '图片上传错误');
                }
            }
            CommonUtil::treatImg('mediaLog', $imgsArray);
        }
        $params["userinfoId"] = (int)$shopId;
        $params["saleUri"] = (string)$saleUri;
        $params["content"] = (string)$content;
        $params["reason"] = (string)$reason;
        $params["imgs"] = json_encode(array("imgs" => $imgsArray));
        $params["imgsList"] = (string)implode($imgList, ",");
        $error = $this->validate($params, ['userinfoId' => 'required', 'saleUri' => 'required', 'content' => 'required', 'reason' => 'required', 'imgs' => 'required']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::SAVE_RATE_APPEAL, $params);
        $this->dealResultData($ret, function ($data) {
            if ($data == 1) {
                return true;
            }
            return false;
        });
        return $ret;
    }

    function cancelRateAppeal(int $shopId, string $saleUri)
    {
        $params["userinfoId"] = (int)$shopId;
        $params["saleUri"] = (string)$saleUri;
        $error = $this->validate($params, ['userinfoId' => 'required', 'saleUri' => 'required']);
        if ($error) {
            return $error;
        }
        $ret = $this->httpPost(Router::CANCEL_RATE_APPEAL, $params);
        $this->dealResultData($ret, function ($data) {
            if ($data == 1) {
                return true;
            }
            return false;
        });
        return $ret;
    }

    public function getRateImgList($media, $size = 240, $num = 5)
    {
        $imgList = [];
        if ($imgs = get_property($media, 'imgs', [])) {
            foreach ($imgs as $key => $img) {
                if ($key >= $num) {
                    break;
                }
                $imgList[] = CommonUtil::combineImgUrl($img, $size);
            }
        }
        return $imgList;
    }
}
