<?php


namespace App\Utils;

use App\Libraries\rabbitmq\V2\Rabbit_mq_client;
use WptCommon\Library\Facades\MLogger;

/**
 * Class ImageUtil
 * @package App\Utils
 */
class ImageUtil
{
    /**
     * 证书
     */
    const CERT = 'certImg';

    /**
     * 公博
     */
    const GB = 'gbImg';

    /**
     * 认证
     */
    const VERIFY = 'verifyImg';

    /**
     * 证书水印
     */
    const MCERT = 'mcertImg';

    /**
     * 拍品
     */
    const SALE = 'saleImg';

    /**
     * 头图
     */
    const HEADER = 'headerImg';

    /**
     * @param $url
     * @return mixed
     */
    private static function assembleCdnUrl($url)
    {
        return sprintf("%s%s", config('app.CDNURL'), $url);
    }


    /**
     * 拼接图片URL
     * @param string $image 图片主体
     * @param int $size 大小
     * @return string
     */
    public static function combineImgUrl($image, $size = 0)
    {
        if (strpos($image, '/') === false) {
            $image = 'img/' . $image;
        }
        if ($size > 0) {
            $image .= '/w/' . $size;
        }
        return self::assembleCdnUrl($image);
    }


    /**
     * 拍品所有图片处理
     * @param $imgs
     * @param int $size
     * @param int $countLimit
     * @return array
     */
    public static function combineSaleImgUrl($imgs, $size = 240, $countLimit = 9)
    {
        $saleImgList = [];
        foreach ($imgs as $key => $v) {
            //图片素材id<10，非正常图片
            if (strlen($v) < 10) {
                continue;
            }

            if ($key >= $countLimit) {
                break;
            }
            //兼容,修复发送CDN域名的图片
            $v = str_replace(config('app.CDNURL') . 'img/', '', $v);
            $saleImgList[] = self::combineImgUrl($v, $size);
        }

        return $saleImgList;
    }


    /**
     * 拼接认证图片URL
     * @param string $image 图片主体
     * @param int $size 大小
     * @param string $type
     * @return string
     */
    public static function combineCertifyImgUrl($image, $size = 0, $type = '')
    {
        //打水印
        if ($type == 'mark') {
            $image = 'mcert/' . $image;
        } else {
            $image = 'certify/' . $image;
        }

        if ($size > 0) {
            $image .= '/w/' . $size;
        }
        return self::assembleCdnUrl($image);
    }

    /**
     * 拼接图片URL
     * @param $image
     * @param int $size
     * @return string
     */
    public static function combineStaticImgUrl($image, $size = 0)
    {
        if (strpos($image, '/') === false) {
            $image = 'static/' . $image;
        }
        if ($size > 0) {
            $image .= '/w/' . $size;
        }
        return self::assembleCdnUrl($image);
    }

    /**
     * 拼接云仓图片URL
     * @param $bucket
     * @param $image
     * @param int $size
     * @return string
     */
    public static function combineImgUrlByBucket($bucket, $image, $size = 0)
    {
        if (strpos($image, '/') === false) {
            $image = $bucket . '/' . $image;
        }
        if ($size > 0) {
            $image .= '/w/' . $size;
        }
        return self::assembleCdnUrl($image);
    }

    /**
     * 拼接图片URL
     * @param string $image 图片主体
     * @param int $size 大小
     * @return string
     */
    public static function combineOrderImgUrl($image, $size = 0)
    {
        $url = 'img/' . $image;
        if ($size > 0) {
            $url .= '/w/' . $size;
        }
        return self::assembleCdnUrl($url);
    }


    /**
     * 拼接公博图片URL
     * @param string $image 图片主体
     * @param int $size 大小
     * @return string
     */
    public static function combineGbImgUrl($image, $size = 0)
    {
        $url = 'gb/' . $image;
        if ($size > 0) {
            $url .= '/w/' . $size;
        }
        return self::assembleCdnUrl($url);
    }

    /**
     * 图片写入文件给运维处理
     * @param $imgs
     * @param $channel
     * @param bool $isArticle
     * @param string $type
     * @return bool
     */
    public static function sendImageToQueue($imgs, $channel, $isArticle = false, $type = '')
    {
        if (empty($imgs)) {
            return false;
        }
        return self::enqueue(self::filterImg($imgs, $isArticle), $channel, $type);
    }

    /**
     * 不校验图片
     * @param $img
     * @param $channel
     * @return bool
     */
    public static function sendImageToQueueSingle($img, $channel)
    {
        if (empty($img) || is_array($img)) {
            return true;
        }
        return self::enqueue([$img], $channel);
    }

    /**
     * @param $imgs
     * @param $channel
     * @param $type
     * @return bool
     */
    private static function enqueue($imgs, $channel, $type = '')
    {
        if (empty($imgs)) {
            return false;
        }
        $rabbitMqImg = new Rabbit_mq_client('rabbitMqImg');
        foreach ($imgs as $img) {
            $ret = $rabbitMqImg->pushImgMessage($channel, $type . $img);
            MLogger::info('rabbitMqTreatImg', __CLASS__ . "_" . $channel, ['img' => $img]);
            if (isset($ret['status']['code']) && $ret['status']['code'] != 0) {
                MLogger::info('rabbitMqTreatImgError', $channel, ['img' => $img]);
            }
        }
        return true;
    }

    /**
     * @param $imgs
     * @param bool $isArtcle
     * @return array
     */
    private static function filterImg($imgs, $isArtcle = false)
    {
        $validImg = [];
        foreach ($imgs as $img) {
            $img = trim($img);
            if (is_numeric(substr($img, 0, 8))) {
                if (substr($img, 0, 8) > date('Ymd', strtotime("-2 day"))) {
                    $validImg[] = $img;
                }
            } else {
                if (substr($img, 10, 8) > date('Ymd', strtotime("-2 day")) && $isArtcle) {
                    $validImg[] = $img;
                }
            }
        }
        return $validImg;
    }

    /**
     * 头像图片（格式化user srv返回格式）
     * @param $headImgUri
     * @param int $flag
     * @return string
     */
    public static function headImgUriByUserService($headImgUri, $flag = 0)
    {
        $userinfo = app('DefaultUserinfo');
        if (get_property($userinfo, 'platform', '') == 'wechat') {
            if (PROTOCAL == 'https:' && strpos($headImgUri, 'http://') !== false) {
                $headImgUri = str_replace('http://', 'https://', $headImgUri);
            }
        }
        if (substr($headImgUri, -2) == '/0') {
            $headImgUri = preg_replace('/(\/\d+$)/', "/{$flag}", $headImgUri);
        }
        return $headImgUri;
    }

    /**
     * 头像图片
     * @param $headimgurl
     * @param int $flag
     * @return mixed|string
     */
    public static function headimgurl($headimgurl, $flag = 0)
    {
        $userinfo = app('DefaultUserinfo');
        $cdnUrl = 'https:' . env('CDNURL');
        // $flag =0、46、64、96、132
        if (strlen($headimgurl) < 10) {
            return $cdnUrl . 'res/img/nohead.jpg';
        }

        if (preg_match('/([a-zA-z]+:\/\/\w+\.qlogo\.cn\/)/', $headimgurl)) {
            //微信公共账号+小程序
            $headimgurl = preg_replace('/([a-zA-z]+:\/\/\w+\.qlogo\.cn\/)/', $cdnUrl, $headimgurl);

            //兼容小程序
            $headimgurl = str_replace('vi_32/', '', $headimgurl);

            return preg_replace('/(\/\d+$)/', "/w/{$flag}", $headimgurl);
        } elseif (strpos($headimgurl, 'image.myqcloud.com') !== false) {
            //万象优图
            $headimgurl = preg_replace('/([a-zA-z]+:\/\/appwpt-10002380\.image\.myqcloud\.com\/)/', $cdnUrl . 'mmopen/', $headimgurl);
            $headimgurl .= "/w/{$flag}";
            return $headimgurl;
        } elseif (strpos($headimgurl, 'sinaimg') !== false) {
            //新浪
            $imgUrlSub = substr($headimgurl, 0, strripos($headimgurl, '/'));
            $_headimgurl = str_replace($imgUrlSub, $cdnUrl . 'mmopen', $headimgurl);
            $_headimgurl .= "/w/{$flag}";
            return $_headimgurl;
        } else {
            if (substr($headimgurl, -4) == '/w/0') {
                $headimgurl = preg_replace('/(\/\d+$)/', "/{$flag}", $headimgurl);
            } elseif (substr($headimgurl, -2) == '/0') {
                $headimgurl = preg_replace('/(\/\d+$)/', "/w/{$flag}", $headimgurl);
            }

            if (get_property($userinfo, 'platform', '') == 'wechat') {
                if (PROTOCAL == 'https:' && strpos($headimgurl, 'http://') !== false) {
                    $headimgurl = str_replace('http://', 'https://', $headimgurl);
                }
            }

            return $headimgurl;
        }
    }

    public static function headimgurlChangeSize($headimgurl, $flag = 0)
    {
        return str_replace("/w/0", "/w/$flag", $headimgurl);
    }

    /**
     * 缓存图片数据
     * @param $imgUrl
     * @return bool
     */
    public static function asyncCachedImage($imgUrl)
    {
        if (!$imgUrl || (strlen($imgUrl) < 40)) {
            MLogger::info("imgTplCacheInvalid", "请求缓存图片", $imgUrl);
            return false;
        }
        $cacheUrl = env("DRAW_CACHE_URL", "http://draw-vpc.wptqc.com/cache");

        $url = $cacheUrl . '?p=' . $imgUrl;
        $res = CommonUtil::getPageSimple($url);
        MLogger::info('imgTplCache', '请求缓存图片', ['imgUrl' => $imgUrl, 'res' => $res]);
        return true;
    }

    /**
     * 批量发送请求
     *
     * @param $imgList
     * @return bool
     */
    public static function muiltAsyncCachedImage($imgList)
    {
        $cacheUrl = env("DRAW_CACHE_URL", "http://draw-vpc.wptqc.com/cache");
        $imgUrlList = [];
        foreach ($imgList as $imgUrl) {
            if (strlen($imgUrl) < 40) {
                MLogger::info("muiltAsyncCachedImageInvalid", "批量请求缓存图片", $imgUrl);
                continue;
            }
            $imgUrlList[] = $cacheUrl . '?p=' . $imgUrl;
        }
        CommonUtil::getMultiPageSimple($imgUrlList);
        return true;
    }

    /**
     * 原方法ImageBufferUtil::putfile
     * @param $param
     * @return bool|mixed
     */
    public static function putFile($param)
    {
        $delimiter = uniqid();
        $url = env("DRAW_UPLOAD_URL", "http://draw-vpc.wptqc.com/upload");

        $post_data = static::buildData($param, $delimiter);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: multipart/form-data; boundary=" . $delimiter,
            "Content-Length: " . strlen($post_data)
        ]);
        $response = curl_exec($curl);
        $errorCode = curl_errno($curl);
        $errorMsg = curl_error($curl);
        curl_close($curl);
        if ($errorCode > 0) {
            MLogger::info('imageBufferPost', 'curl_error_info', ['data' => $param, 'msg' => $errorMsg]);
            return false;
        }
        $info = json_decode($response, true);
        if (isset($info['code']) && $info['code'] != 0) {
            MLogger::info('imageBufferPost', 'code_error_info', ['data' => $param, 'msg' => '程序异常']);
            return false;
        }
        return $info['Data'][$param['filename']];
    }

    private static function buildData($param, $delimiter)
    {
        $data = '';
        $eof = "\r\n";
        $upload = $param['file'];
        unset($param['file']);

        foreach ($param as $name => $content) {
            $data .= "--" . $delimiter . "\r\n"
                . 'Content-Disposition: form-data; name="' . $name . "\"\r\n\r\n"
                . $content . "\r\n";
        }
        // 拼接文件流
        $data .= "--" . $delimiter . $eof
            . 'Content-Disposition: form-data; name="filename"; filename="' . $param['filename'] . '"' . "\r\n"
            . 'Content-Type:application/octet-stream'."\r\n\r\n";

        $data .= $upload . "\r\n";
        $data .= "--" . $delimiter . "--\r\n";
        return $data;
    }
}
