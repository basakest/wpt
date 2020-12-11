<?php

namespace App\Utils;

use App\Cache\LinkCache;
use App\Cache\QueueCache;
use App\Client\Go\Http;
use App\CodisCache\UseLessCodisCache;
use App\CodisCache\UserCodisCache;
use App\ConstDir\BaseConst;
use App\ConstDir\CookieConst;
use App\ConstDir\RegularConst;
use App\ConstDir\ScConst;
use App\Exceptions\ApiException;
use App\Facades\WuKong\WuKong;
use App\Libraries\common\alarm\src\alarmconfig;
use App\Libraries\common\alarm\src\rqalarm;
use App\Libraries\rabbitmq\V2\Rabbit_mq_client;
use Illuminate\Support\Facades\Cookie;
use Sentry\Request\SingleMultiRequest;
use Sentry\Request\SingleRequest;
use WptCommon\Library\Facades\MLogger;
use WptUtils\Http\Client;
use WptUtils\Poster\Poster;
use WptUtils\Str;

class CommonUtil
{
    /**
     * @param $code
     * @param $msg
     * @throws ApiException
     */
    public static function throwException($code, $msg)
    {
        throw new ApiException($code, $msg);
    }

    /**
     * 取得随机代码,并上锁60秒
     * @param int $length
     * @param string $type
     * @param bool $isNumeric
     * @return string
     */
    public static function createRandStr($length = 32, $type = '', $isNumeric = false)
    {
        $chars = $isNumeric ? "0123456789" : "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        if ($type) {
            $key = 'randstr_' . $type . '_' . $str;
        } else {
            $key = 'randstr_' . $str;
        }
        if (LockUtil::lock($key, 60)) {
            return self::createRandStr($length, $type, $isNumeric);
        } else {
            return $str;
        }
    }

    /**
     * 取得随机代码,并上锁60秒
     * @param int $length
     * @param string $type
     * @param bool $isNumeric
     * @return string
     */
    public static function createUriRandStr($length = 32, $type = '', $isNumeric = false)
    {
        $chars = $isNumeric ? "0123456789" : "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        if ($type) {
            $key = 'randstr_' . $type . '_' . $str;
        } else {
            $key = 'randstr_' . $str;
        }
        if (LockUtil::uriLock($key, 60)) {
            return self::createRandStr($length, $type, $isNumeric);
        } else {
            return $str;
        }
    }


    /* 获取分享链接
     * $uri userinfo或sale的uri
     * $type goods商品，shop店铺
     *
     */
    public static function getShareUrl($uri, $type = 'uri')
    {
        $_shareDomainConfig = [
            config('app.WEI_HOST')
        ];

        $key = mt_rand(0, (count($_shareDomainConfig) - 1));
        $url = $_shareDomainConfig[$key] . $type . "/" . $uri;
        return $url;
    }


    /**
     * @param $url
     * @param int $ttl
     * @return string|null
     */
    public static function getShortUrlCode($url, $ttl = 0)
    {
        if ($url) {
            $md5 = md5($url);

            $urlData = LinkCache::getMd5Url($md5);
            if ($urlData !== false) {
                $code = $urlData['code'];
            } else {
                $n = 1;
                do {
                    $code = Str::randString(8);
                    $n++;
                } while (LinkCache::getShortUrl($code) !== false && $n <= 9);

                LinkCache::setMd5Url($md5, ['code' => $code, 'url' => $url], $ttl);
                LinkCache::setShortUrl($code, $url, $ttl);
            }
            return $code;
        }
        return null;
    }

    /**
     * 转化金额 100010分  ->   1000.10 元
     * @param $money
     * @return string
     */
    public static function formatFenToYuan($money)
    {
        return fenToYuan(intval($money));
    }


    /**
     * 取得URI
     * @param int $len
     * @param string $pre
     * @return string
     */
    public static function getUri($len = 6, $pre = '')
    {
        return $pre . date("ymdHi") . Str::randString($len);
    }

    /**
     * 过滤关键字
     * @param $data
     * @param array $ignore 不要过滤的关键字数组
     * @return mixed
     */
    public static function filterWords($data, $ignore = [])
    {
        $_filterWords = BaseConst::FILTER_WORDS;
        if ($ignore) {
            $_filterWords = array_diff($_filterWords, $ignore);
        }

        $data = preg_replace_callback('[' . implode('|', $_filterWords) . ']', function ($matches) {
            if ($matches[0]) {
                if (function_exists('mb_strlen')) {
                    $strlen = mb_strlen($matches[0], 'utf-8');
                } elseif (function_exists('iconv_strlen')) {
                    $strlen = iconv_strlen($matches[0], 'utf-8');
                } else {
                    $strlen = strlen($matches[0]);
                }
                return implode("", array_fill(0, $strlen, "*"));
            }
            return "";
        }, $data);

        return $data;
    }

    /**
     * 生成订单号
     * @return string
     */
    public static function createOutTradeNo()
    {
        return date('ymdHi') . self::createRandStr(6, 'balance');
    }

    /**
     * 取得uri
     * @param string $prefix
     * @param int $randLength
     * @return string
     */
    public static function createUri($prefix = '', $randLength = 6)
    {
        return $prefix . date("ymdHi") . CommonUtil::createRandStr($randLength);
    }

    /**
     * 取得uri
     * @param string $prefix
     * @param int $randLength
     * @return string
     */
    public static function createNewUri($prefix = '', $randLength = 6)
    {
        return $prefix . date("ymdHi") . CommonUtil::createUriRandStr($randLength);
    }

    /**
     * 为了安全只请求一次CURL
     * @param $url
     * @param $data
     * @param int $timeout
     * @param string $postType
     * @return mixed
     * @see Client::post()
     * @deprecated
     */
    public static function postPageSimple($url, $data, $timeout = 1, $postType = 'string')
    {
        $uniqueId = md5(uniqid() . rand(100000, 999999));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //连接时间
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        //返回响应时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        curl_setopt($ch, CURLOPT_POST, 1); // 发送一个常规的Post请
        if ('array' == $postType) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Post提交的数据包求
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($data) ? $data : http_build_query($data)); // Post提交的数据包求
        }
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'unique_id: ' . $uniqueId,
                'trace_id:' . (defined('TRACE_ID') ? TRACE_ID : ''),
            ]
        );

        $result = curl_exec($ch);

        $errorCode = curl_errno($ch);
        if (curl_errno($ch) > 0) {
            MLogger::info(
                'postPageSimple',
                'error_info',
                ['url' => $url, 'ip' => IpUtil::getRealIp(), 'errorCode' => $errorCode, 'data' => $data, 'unique_id' => $uniqueId]
            );
        }
        curl_close($ch);
        return $result;
    }

    /**
     * 为了安全只请求一次CURL
     * @param $url
     * @param int $timeout
     * @param string $refer
     * @return mixed
     * @deprecated
     * @see Client::get()
     */
    public static function getPageSimple($url, $timeout = 1, $refer = '')
    {
        $uniqueId = md5(uniqid() . rand(100000, 999999));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //连接时间
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        //返回响应时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        if ($refer) {
            curl_setopt($ch, CURLOPT_REFERER, $refer); //构造来路
            curl_setopt(
                $ch,
                CURLOPT_USERAGENT,
                'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5'
            );
        }
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'unique_id:' . $uniqueId,
                'trace_id:' . (defined('TRACE_ID') ? TRACE_ID : ''),
            ]
        );

        $result = curl_exec($ch);

        $errorCode = curl_errno($ch);
        if (curl_errno($ch) > 0) {
            MLogger::info('getPageSimple', 'error_info', ['url' => $url, 'errorCode' => $errorCode, 'unique_id' => $uniqueId]);
        }
        curl_close($ch);
        return $result;
    }

    /**
     * 为了安全只请求一次CURL
     * @param $urlData
     * @param int $timeout
     * @param string $refer
     * @return mixed
     * @deprecated
     * @see Client::add()
     */
    public static function getMultiPageSimple($urlData, $timeout = 1, $refer = '')
    {
        // 创建批处理cURL句柄
        $mh = curl_multi_init();
        foreach ($urlData as $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            //连接时间
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            //返回响应时间
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

            if ($refer) {
                curl_setopt($ch, CURLOPT_REFERER, $refer); //构造来路
                curl_setopt(
                    $ch,
                    CURLOPT_USERAGENT,
                    'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5'
                );
            }

            curl_multi_add_handle($mh, $ch);  //向批处理句柄中,添加句柄   后面批量的去模拟访问,抓取回资源
        }

        $result = [];
        $active = null;
        do {
            while (($mrc = curl_multi_exec($mh, $active)) == CURLM_CALL_MULTI_PERFORM) {
                ;
            }

            if ($mrc != CURLM_OK) {
                break;
            }

            // a request was just completed -- find out which one
            while ($done = curl_multi_info_read($mh)) {
                // get the info and content returned on the request
                $res = curl_multi_getcontent($done['handle']);
                if ($res) {
                    $res = json_decode($res, true);
                    if (!empty($res) && isset($res['code']) && $res['code'] == 0) {
                        $result[] = $res['data'];
                    }
                }
                // $responses[$map[(string) $done['handle']]] = compact('info', 'error', 'results');

                // remove the curl handle that just completed
                curl_multi_remove_handle($mh, $done['handle']);
                curl_close($done['handle']);
            }

            // Block for data in / output; error handling is done by curl_multi_exec
            if ($active > 0) {
                curl_multi_select($mh);
            }
        } while ($active);

        return $result;
    }

    /**
     * bi数据上报,毫秒级,为了安全只请求一次CURL
     * @param $url
     * @param $data
     * @param int $millisecond
     * @return mixed
     * @deprecated
     * @see Client::post()
     */
    public static function biPostMillisecondPageSimpleReturnError($url, $data, $millisecond = 300)
    {
        $uniqueId = md5(uniqid() . rand(100000, 999999));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //连接时间
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        //返回响应时间
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $millisecond);//毫秒级

        curl_setopt($ch, CURLOPT_POST, 1);                                                        // 发送一个常规的Post请
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($data) ? $data : http_build_query($data)); // Post提交的数据包求
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'unique_id:' . $uniqueId,
                'trace_id:' . (defined('TRACE_ID') ? TRACE_ID : ''),
            ]
        );

        $result = curl_exec($ch);

        $errorCode = curl_errno($ch);
        $errorMsg = curl_getinfo($ch);
        if ($errorCode > 0) {
            MLogger::info(
                'biPostMillisecondPageSimpleReturnError',
                'error_info',
                ['url' => $url, 'errorCode' => $errorCode, 'errorMsg' => $errorMsg, 'data' => $data, 'unique_id' => $uniqueId]
            );
        }
        curl_close($ch);
        return ['result' => $result, 'errorCode' => $errorCode];
    }

    /**
     * 上报BI数据
     * @param $data
     * @param bool $requestData
     * @return bool
     */
    public static function reportBIData($data, $requestData = true)
    {
        if (!BaseConst::BI_OPEN) {
            return false;
        }
        // 如果是匿名id，uuri填写匿名id
        if (empty($data['uuri']) && !empty(get_property(app('DefaultUserinfo'), 'touristUri'))) {
            $data['uuri'] = get_property(app('DefaultUserinfo'), 'touristUri');
        }
        $nowTime = time();
        empty($data['time']) && $data['time'] = $nowTime;
        if (PHP_SAPI != "cli" && $requestData == true) {
            $data['requestData'] = [
                'ip' => IpUtil::getRealIp(),
                'href' => self::getFullUrl(),
                'referer' => DeviceUtil::getReferUrl(),
                // DeviceDetectUtil
                'platform' => !empty($data['platform']) ? $data['platform'] : DeviceUtil::getPlatform(),
                'sVersion' => BaseConst::WPT_VERSION,
                'userAgent' => urlencode(DeviceUtil::getUserAgent()),
                'sessionId' => CookieUtil::getSessionId(),
                'identity' => Cookie::get(CookieConst::USER_TRACE_IDENTITY, '')
            ];

            //只有app才上报
            if (DeviceUtil::isApp()) {
                $userAgentStr = DeviceUtil::getUserAgent();
                $tmpArr = explode(' ', $userAgentStr);
                $middleArr = [];
                foreach ($tmpArr as $v) {
                    $middleArr[] = explode('/', $v);
                }
                $wholeArr = [];
                foreach ($middleArr as $w) {
                    $wholeArr[$w[0]] = $w[1] ?? '';
                }
                $data['requestData']['cVersion'] = $wholeArr['WptMessenger'] ?? '';
                $data['requestData']['os'] = !empty($wholeArr['OS']) ? strtolower($wholeArr['OS']) : 'android';
                $data['requestData']['deviceId'] = $wholeArr['DeviceId'] ?? '';
                $data['requestData']['channel'] = $wholeArr['Channel'] ?? '';

                //APP链路跟踪
                if (!empty($wholeArr['identity'])) {
                    $data['requestData']['identity'] = $wholeArr['identity'];
                }
            }

            if (!preg_match("/(&|\?)r=[^&]+/", $data['requestData']['href'])) {
                $_urlData = parse_url($data['requestData']['referer']);
                if (!empty($_urlData['query'])) {
                    $_urlSearchList = explode('&', $_urlData['query']);
                    if ($_urlSearchList) {
                        foreach ($_urlSearchList as $_search) {
                            if ($_search) {
                                $_searchList = explode('=', $_search);
                                if ($_searchList[0] == 'r' && !empty($_searchList[1])) {
                                    $_href = preg_replace("/(&|\?)r=([^&]*)/", "$1r=" . $_searchList[1], $data['requestData']['href']);
                                    if ($data['requestData']['href'] == $_href) {
                                        $_hrefStr = (strpos($_href, "?") === false ? "?" : "&");
                                        $data['requestData']['href'] .= $_hrefStr . "r=" . $_searchList[1];
                                    } else {
                                        $data['requestData']['href'] = $_href;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //来源判断
            if (!empty($data['r'])) {
                $data['requestData']['href'] = $data['r'];
                $data['requestData']['referer'] = $data['r'];
                unset($data['r']);
            }

            $sc = Cookie::get(CookieConst::SCENECHANNEL);
            if (!empty($sc)) {
                $data['sc'] = $sc;
            }
        }

        if (!empty($data['platform'])) {
            unset($data['platform']);
        }

        // 给Roi广告上报第三方用
        if (get_property($data, 'type', '') == 'bid') {
            $roiData = self::getRoiData($data['uid'] ?? 0);
            if (!empty($roiData) && !empty($roiData['channel']) && $roiData['isApp']) {
                $scArr = explode('_', $roiData['channel']);
                if (in_array($scArr[0], ScConst::BID_SC_UP)) {
                    $giveData = array_merge($data, ['sc' => $roiData['channel'], 'isApp' => 1]);
                    MLogger::info('upRoiLog', '上报roi记录', $giveData);
                    try {
                        WuKong::delivery(
                            config('app.ROI_HOST') . 'h/inside/basic/order-bid-i',
                            ['param' => wpt_json_encode($giveData)],
                            0
                        );
                    } catch (\Exception $e) {
                        MLogger::error('upRoiError', '调用悟空上报roi出错', ['error' => $e->getMessage()]);
                    }
                }
            }
        }

        // 生成唯一链路id
        $data['uuid'] = Str::uuid($data);
        MLogger::info(['hourly', 'reportBIDataUuid'], 'BI_REPORT_UUID' . $data['uuid'], ['data' => $data]);
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        // 上报数据
        $url = 'http:' . env('BI_HOST') . 'ereport';
        $ret = self::biPostMillisecondPageSimpleReturnError($url, strval($data));
        // 如果存在问题，则push到异步队列，进行重新上报
        if (!empty($ret) && is_array($ret) && !empty($ret['errorCode'])) {
            QueueCache::lpushPostMillisecondPageSimpleTryAsync([
                'url' => $url,
                'data' => $data,
                'result' => !empty($ret['result']) ? $ret['result'] : '',
                'errorCode' => $ret['errorCode']
            ]);
        }

        return true;
    }

    public static function getFullUrl()
    {
        $requestUri = '';
        if (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
        } else {
            if (isset($_SERVER['argv'])) {
                $requestUri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['argv'][0];
            } elseif (isset($_SERVER['QUERY_STRING'])) {
                $requestUri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
            }
        }

        $scheme = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
        $protocol = strstr(strtolower($_SERVER["SERVER_PROTOCOL"]), "/", true) . $scheme;

        return $protocol . "://" . $_SERVER['SERVER_NAME'] . $requestUri;
    }

    /**
     * 隐藏姓名
     * @param $name
     * @return string
     */
    public static function hideName($name)
    {
        if (empty($name)) {
            return $name;
        }
        $doubleSurname = [
            '欧阳',
            '太史',
            '端木',
            '上官',
            '司马',
            '东方',
            '独孤',
            '南宫',
            '万俟',
            '闻人',
            '夏侯',
            '诸葛',
            '尉迟',
            '公羊',
            '赫连',
            '澹台',
            '皇甫',
            '宗政',
            '濮阳',
            '公冶',
            '太叔',
            '申屠',
            '公孙',
            '慕容',
            '仲孙',
            '钟离',
            '长孙',
            '宇文',
            '司徒',
            '鲜于',
            '司空',
            '闾丘',
            '子车',
            '亓官',
            '司寇',
            '巫马',
            '公西',
            '颛孙',
            '壤驷',
            '公良',
            '漆雕',
            '乐正',
            '宰父',
            '谷梁',
            '拓跋',
            '夹谷',
            '轩辕',
            '令狐',
            '段干',
            '百里',
            '呼延',
            '东郭',
            '南门',
            '羊舌',
            '微生',
            '公户',
            '公玉',
            '公仪',
            '梁丘',
            '公仲',
            '公上',
            '公门',
            '公山',
            '公坚',
            '左丘',
            '公伯',
            '西门',
            '公祖',
            '第五',
            '公乘',
            '贯丘',
            '公皙',
            '南荣',
            '东里',
            '东宫',
            '仲长',
            '子书',
            '子桑',
            '即墨',
            '达奚',
            '褚师',
            '吴铭'
        ];
        $surname = mb_substr($name, 0, 2);
        if (in_array($surname, $doubleSurname)) {
            $repName = '**' . mb_substr($name, 2, (mb_strlen($name, 'UTF-8')));
        } else {
            $repName = '*' . mb_substr($name, 1, (mb_strlen($name, 'UTF-8')));
        }
        return $repName;
    }

    /**
     *  315风控产品 拍品存在则 分类改为6000
     * @param $content
     * @return bool
     */
    public static function getRiskWorkBy315($content)
    {
        $riskWords = BaseConst::RISK_WORDS;
        $unRiskWords = BaseConst::UN_RISK_WORDS;
        $flag = false;
        foreach ($riskWords as $word) {
            if (stripos($content, $word) !== false) {
                $flag = true;
                //关键字有特殊的数组
                if (!empty($unRiskWords[$word])) {
                    foreach ($unRiskWords[$word] as $u) {
                        if (stripos($content, $u) !== false) {
                            $flag = false;
                        }
                    }
                }

                //一旦查出就返回。不用再次循环
                if ($flag == true) {
                    return $flag;
                }
            }
        }
        return $flag;
    }

    /**
     * 返回成功 （内部业务使用）
     * @param array $data 数据对象
     * @param int $code 返回状态
     * @return array [data,code]
     */
    public static function returnSuccess($data = null, $code = 1001)
    {
        $result = [
            'data' => $data,
            'code' => $code
        ];
        return $result;
    }

    /**
     * 返回失败 (内部业务使用)
     * @param string $msg 错误信息
     * @param int $code 错误编号
     * @return array [code,msg]
     */
    public static function returnFail($msg = '', $code = 1000)
    {
        $result = [
            'code' => $code,
            'msg' => $msg
        ];
        return $result;
    }

    /**
     * @param $uri
     * @throws ApiException
     */
    public static function checkUri($uri)
    {
        if (!preg_match(RegularConst::URI, $uri)) {
            self::throwException(100, '参数错误');
        }
    }


    /** B端业务组报警
     * @param $content string 钉钉群提示文字
     * @param array | string $atMobiles
     * @return bool
     */
    public static function bGroupAlert($content, $atMobiles = [])
    {
        rqalarm::getInstance()
            ->setGroupId(alarmconfig::GROUP_DEVELOP1)
            ->setAlarmMsg($content)
            ->setInformUsers($atMobiles)
            ->send();
        return true;
    }

    /**
     * C端业务组钉钉报警
     * @param $content string 钉钉群提示文字
     * @param array | string $atMobiles
     * @return bool
     */
    public static function cGroupAlert($content, $atMobiles = [])
    {
        rqalarm::getInstance()
            ->setGroupId(alarmconfig::GROUP_DEVELOP2)
            ->setAlarmMsg($content)
            ->setInformUsers($atMobiles)
            ->send();
        return true;
    }

    /**
     * 获取traceId
     * @param null $traceId
     * @return string|null
     */
    public static function traceId($traceId = null)
    {
        static $staticTraceId = null;

        if ($traceId) {
            $staticTraceId = $traceId;
        }

        if ($staticTraceId) {
            return $staticTraceId;
        }

        if (defined('TRACE_ID')) {
            return TRACE_ID;
        }

        if (empty($staticTraceId)) {
            $staticTraceId = uniqid('', true) . '.' . rand(100000, 999999);
        }

        return $staticTraceId;
    }

    /**
     * @param $content
     * @param array $atMobiles
     * @return bool
     */
    public static function traceBGroupAlert($content, $atMobiles = [])
    {
        return self::liveGroupAlert("[" . self::traceId() . "]" . $content, $atMobiles);
    }

    /**
     * 是否显示一元拍角标，版本小于2.0不显示
     * @return bool
     */
    public static function isShowSuperscript()
    {
        $isShowSuperscript = true;
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agentArr = DeviceUtil::convertAppUserAgentArr($_SERVER['HTTP_USER_AGENT']);
            if (isset($agentArr['WptMessenger'])) {
                $isShowSuperscript = current(explode('.', $agentArr['WptMessenger'])) > 1;
            }
        }
        return $isShowSuperscript;
    }

    /**
     * push任务到wukong
     * @param $crondType
     * @param $params
     * @param $taskName
     * @param int $time
     * @return bool
     */
    public static function wukongTask($crondType, $params, $taskName, $time = 0)
    {
        $payload = [];
        if (in_array($crondType, ['crond-api', 'k8s-crond-api', 'k8s-crond-api-gray'])) {
            $payload[] = '/data/www/api.weipaitang.com/artisan';
        } elseif ($crondType == 'k8s-crond-jiepai') {
            $payload[] = '/data/www/api.weipaitang.com/wptartisan';
        } else {
            $payload[] = '/data/www/w.weipaitang.com/index.php';
        }
        foreach ((array)$params as $param) {
            $payload[] = (string)$param;
        }
        try {
            $taskName = 'wukong_task_' . $taskName;
            $ret = WuKong::task([$crondType], $taskName, $time, "/usr/bin/php", $payload);
            MLogger::info('wukongTask', $taskName, ['crondType' => $crondType, 'time' => $time, 'payload' => $payload]);

            if (!$ret) {
                MLogger::error('wukongTask', '悟空请求结果返回异常', ['crondType' => $crondType, 'time' => $time, 'payload' => $payload]);
                return false;
            }
        } catch (\Exception $e) {
            MLogger::error('wukongTask', '悟空请求出现异常', ['msg' => $e->getMessage(), 'payload' => $payload]);
            return false;
        }

        return true;
    }

    /**
     * 批量请求http接口
     * @param array $requestArr
     * @param $service //类似u.ua.getByType
     * @param $param //参数
     * @param string $version
     * @return array
     */
    public static function multHttpRequest(array $requestArr, $service, $param, $version = "v1.0.0"): array
    {
        $response = [];
        if ($requestArr && $service) {
            // 初始化客户端
            $newRequestArr = array_chunk($requestArr, 200);
            foreach ($newRequestArr as $val) {
                $singleMulti = new SingleMultiRequest();
                foreach ($val as $onlyKey) {
                    //获取参数
                    $params = self::getMultHttpRequest($service, $onlyKey, $param);
                    $r1 = new SingleRequest();
                    $r1->setService((string)$service);
                    $r1->setParams($params);
                    $r1->setVersion($version);
                    $singleMulti->addRequest("{$onlyKey}", $r1);
                }
                //批量请求换成一次请求
                $res = Http::request($singleMulti);
                $_response = [];
                if ($res && true == $res->success && !empty($res->data)) {
                    $_response = $res->data;
                }

                $response += $_response;
            }
        }
        return $response;
    }

    /**
     * 拼接批量请求参数
     * @param $service
     * @param $onlyKey
     * @param $param
     * @return array
     */
    public static function getMultHttpRequest($service, $onlyKey, $param)
    {
        $params = [];
        switch ($service) {
            case 'u.ua.getByType':
                $params = [(int)$onlyKey, $param];
                break;
        }

        return $params;
    }

    /**
     * 获取环境
     * @return string
     */
    public static function getApiEnvValue()
    {
        $envVar = Cookie::get('wpt_debug');
        $envKey = 'prd';
        if ($envVar) {
            if ($envVar == '9cb88042edc55bf85c22e89cf880c63b' || $envVar == '8b75e68a81477d54a1fd97aa0eae97b3') {
                $envKey = 'gray';
            }
        }
        return $envKey;
    }

    //出价外挂
    public static function bidWaiGuaUser($userinfoId, $tag = 336, $reason = "")
    {
        $bidWaiGuaUser = UseLessCodisCache::getBidWaiGuaUser($userinfoId, $tag);
        if ($bidWaiGuaUser === false) {
            UseLessCodisCache::setBidWaiGuaUser($userinfoId, $tag, time());
            $args = 'uid=' . (int)$userinfoId . '&tag=' . $tag . '&reason=' . urlencode($reason);
            $url = config('app.WM_API_HOST') . 'inside/api/bind.tag' . '?' . $args;
            WuKong::delivery($url, [], 0);
        }
    }

    /**
     * 应用户体验组需求，需要增加分享参数，方便统计数据
     * 给分享链接增加分享参数
     * @return string
     */
    public static function getShareFrom()
    {
        // DeviceDetectUtil
        $platForm = DeviceUtil::getPlatform();

        if (DeviceUtil::isApp()) {
            $os = DeviceUtil::getOs();
            if ($platForm == DeviceUtil::PLATFORM_WJB_APP) {
                $sf = $platForm . '_' . $os;
            } else {
                $sf = $os;
            }
        } else {
            $sf = $platForm;
        }

        return $sf;
    }

    /**
     * 此投放广告是否满足千人千面
     * @param $favCate
     * @param $item
     * @return bool
     */
    public static function isInFavCate($favCate, $item)
    {
        list($firstFavCateIds, $secondFavCateIds) = $favCate;

        // 没有设置
        if (empty($item['category'])) {
            return true;
        }

        $adCate = json_decode($item['category'], true);

        // 符合一级分类配置要求（求交集，交集为空，则表示喜好的一级分类不在配置里
        $isInFistCate = !empty($adCate['firstCate']) && array_intersect($firstFavCateIds, $adCate['firstCate']);
        // 符合二级分类配置要求（求交集，交集为空，则表示喜好的二级分类不在配置里）
        $isInSecCate = !empty($adCate['secCate']) && array_intersect($secondFavCateIds, $adCate['secCate']);

        // 符合一级分类、符合二级分类
        if ($isInFistCate || $isInSecCate) {
            return true;
        }

        return false;
    }

    /**
     * 获取广告相关数据
     * @param int $uid
     * @return array
     */
    public static function getRoiData($uid = 0)
    {
        $roiData = [
            'ip' => IpUtil::getRealIp(),
            'href' => self::getFullUrl(),
            'referer' => DeviceUtil::getReferUrl(),
            // DeviceDetectUtil
            'platform' => !empty($data['platform']) ? $data['platform'] : DeviceUtil::getPlatform(),
            'userAgent' => urlencode(DeviceUtil::getUserAgent()),
            'sessionId' => CookieUtil::getSessionId(),
            'isApp' => DeviceUtil::isApp(),
            'cVersion' => BaseConst::WPT_VERSION,
            'os' => 'unknown',
            'deviceId' => '',
            'uid' => $uid,
            'identity' => Cookie::get(CookieConst::USER_TRACE_IDENTITY, ''),
            'channel' => Cookie::get(CookieConst::SCENECHANNEL, ''),
        ];

        // isApp
        if ($roiData['isApp']) {
            $tmpArr = explode(' ', DeviceUtil::getUserAgent());
            $middleArr = [];
            foreach ($tmpArr as $v) {
                $middleArr[] = explode('/', $v);
            }
            $wholeArr = [];
            foreach ($middleArr as $w) {
                $wholeArr[$w[0]] = $w[1] ?? '';
            }
            $roiData['cVersion'] = $wholeArr['WptMessenger'] ?? '';
            $roiData['os'] = !empty($wholeArr['OS']) ? strtolower($wholeArr['OS']) : 'unknown';
            $roiData['deviceId'] = $wholeArr['DeviceId'] ?? '';
            $roiData['identity'] = $wholeArr['identity'] ?? '';
            $roiData['channel'] = $wholeArr['Channel'] ?? '';
        }

        // 从redis里弥补
        if (empty($roiData['channel']) && $uid) {
            $roiData['channel'] = UserCodisCache::getUserRoiData($uid, 'channel');
        }

        return $roiData;
    }

    /** 直播业务组报警
     * @param $content string 提示文字
     * @param array | string $atMobiles
     * @return bool
     */
    public static function liveGroupAlert($content, $atMobiles = [])
    {
        rqalarm::getInstance()
            ->setGroupId(alarmconfig::LIVE_GROUP)
            ->setAlarmMsg($content)
            ->setInformUsers($atMobiles)
            ->send();
        return true;
    }

    /**
     * 后端三组企业微信报警
     * @param $content string 企业微信提示文字
     * @param array | string $atMobiles
     * @return bool
     */
    public static function threeGroupAlert($content, $atMobiles = [])
    {
        rqalarm::getInstance()
            ->setGroupId(alarmconfig::GROUP_DEVELOP3)
            ->setAlarmMsg($content)
            ->setInformUsers($atMobiles)
            ->send();
        return true;
    }

    /**
     * 生成小程序分享图片
     * @param $url
     * @param int $width
     * @param int $height
     * @return mixed
     */
    public static function thumbnail2miniProgram($url, $width = 483, $height = 406)
    {
        try {
            $resp = file_get_contents($url.'?imageInfo');
            $imageInfo = json_decode($resp, true);
            if (empty($imageInfo)) {
                MLogger::info('thumbnail2miniProgram', '获取图片基础信息失败', ['url' => $url]);
                return $url;
            }
        } catch (\Exception $e) {
            MLogger::warning('thumbnail2miniProgram', '获取图片基础信息失败', ['url' => $url]);
            return $url;
        }

        // 计算缩略图宽高及偏移量
        $dx = $dy = 0;
        if ($imageInfo['width'] / $imageInfo['height'] > $width / $height) {
            $w = $width;
            $h = $width / $imageInfo['width'] * $imageInfo['height'];
            $dy = ($height - $h) / 2;
        } else {
            $h = $height;
            $w = $height / $imageInfo['height'] * $imageInfo['width'];
            $dx = ($width - $w) / 2;
        }

        // 七牛缩略图
        $thumbnail = $url.'?imageMogr2/v2/thumbnail/'.$width.'x'.$height;

        // 调用海报生成服务
        $data = [
            'share_img' => [
                'ctx' => $thumbnail,
                'w'   => intval($w),
                'h'   => intval($h),
                'dx'  => intval($dx),
                'dy'  => intval($dy),
            ],
        ];
        try {
            $posterLink = Poster::instance()->setProperties($data)->make('share2miniprogram');
        } catch (\Exception $e) {
            MLogger::warning('thumbnail2miniProgram', '生成海报失败', ['data' => $data, 'err'=>$e->getMessage()]);
            return $url;
        }
        return $posterLink;
    }
}
