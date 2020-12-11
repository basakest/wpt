<?php


namespace App\Utils;

use App\ConstDir\ErrorConst;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Request;
use WptCommon\Library\Facades\MLogger;

/**
 * Class DeviceUtil
 * @package App\Utils
 */
class DeviceUtil
{

    // 微拍堂app
    const PLATFORM_APP = 'app';

    // 微鉴宝app
    const PLATFORM_WJB_APP = 'wjbApp';

    // 手机浏览器
    const PLATFORM_WAP = 'wap';

    // cli
    const PLATFORM_CLI = 'cli';

    // 电脑微信客户端
    const PLATFORM_PC_WECHAT = 'pcWechat';

    // PC端
    const PLATFORM_PC = 'pc';

    // 抖音
    const PLATFORM_DOUYIN = 'douyin';

    // 微信
    const PLATFORM_WECHAT = 'wechat';

    // 微信小程序
    const PLATFORM_WECHAT_APP = 'wechatApp';

    // 微信小程序
    const PLATFORM_BAIDU_XCX = 'bdxcx';

    /**
     * 获取头信息
     * @return string
     */
    public static function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    /** 判断是否是微拍堂APP
     * @param int $version
     * @return bool
     */
    public static function isAppBrowser($version = 0)
    {
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            preg_match('/WptMessenger\/([\d.]+)/i', $_SERVER['HTTP_USER_AGENT'], $match);
            if ($match) {
                if ($version > 0 && $match[1] < $version) {
                    return false;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * 判断是否是微信浏览器
     * @param int $ignore
     * @return bool
     */
    public static function isWeixinBrowser($ignore = 0)
    {
        if ($ignore) {
            return true;
        }
        if (empty($_SERVER['HTTP_USER_AGENT'])
            || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false
        ) {
            return true;
        }
        MLogger::info('errorBrowser', 'request_info', [
            'requestUri' => get_property($_SERVER, 'REQUEST_URI', ""),
            'ip' => IpUtil::getRealIp(),
            'userAgent' => get_property($_SERVER, 'HTTP_USER_AGENT', '_SERVER IS EMPTY')
        ]);

        return false;
    }

    /**
     * 获取是否是APP
     * @return bool
     */
    public static function isApp()
    {
        return in_array(self::getPlatform(), [self::PLATFORM_APP, self::PLATFORM_WJB_APP]);
    }

    /**
     * 切割APP头部
     * @param $userAgentStr
     * @return array
     */
    public static function convertAppUserAgentArr($userAgentStr)
    {
        $tmpArr = explode(' ', $userAgentStr);
        $middleArr = [];
        foreach ($tmpArr as $v) {
            $middleArr[] = explode('/', $v);
        }
        $wholeArr = [];
        foreach ($middleArr as $w) {
            $wholeArr[$w[0]] = $w[1] ?? '';
        }
        return $wholeArr;
    }

    /**
     * @param string $userAgent
     * @return bool
     */
    public static function isMobile($userAgent = '')
    {
        //正则表达式,批配不同手机浏览器UA关键词。
        $regex_match = "/(nokia | iphone | android | motorola | ^mot\- | softbank | foma | docomo | kddi | up\.browser | up\.link | ";
        $regex_match .= "htc | dopod | blazer | netfront | helio | hosin | huawei | novarra | CoolPad | webos | techfaith | palmsource | ";
        $regex_match .= "blackberry | alcatel | amoi | ktouch | nexian | samsung | ^sam\- | s[cg]h | ^lge | ericsson | philips | sagem | " .
            "wellcom | bunjalloo | maui | ";
        $regex_match .= "symbian | smartphone | midp | wap | phone | windows ce | iemobile | ^spice | ^bird | ^zte\- | longcos | pantech " .
            "| gionee | ^sie\- | portalmmm | ";
        $regex_match .= "jig\s browser | hiptop | ^ucweb | ^benq | haier | ^lct | opera\s * mobi | opera\*mini | 320×320 | 240×320" .
            " | 176×220)/i";

        return isset($_SERVER['HTTP_X_WAP_PROFILE'])
            or isset($_SERVER['HTTP_PROFILE'])
            or preg_match($regex_match, strtolower($userAgent == '' ? self::getUserAgent() : $userAgent));
    }

    /**
     * 获取userAgent里面的一个Param
     * @param $paramKey
     * @return mixed|string
     */
    public static function getUserAgentParam($paramKey)
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (empty($userAgent)) {
            return "";
        }
        $userAgent = explode(" ", $userAgent);
        $userAgentParams = [];
        foreach ($userAgent as $item) {
            $userAgentParams [] = explode("/", $item);
        }
        foreach ($userAgentParams as $userAgentParam) {
            if ($userAgentParam[0] == $paramKey && isset($userAgentParam[1])) {
                return $userAgentParam[1];
            }
        }
        return "";
    }

    /**
     * 取平台
     *
     * @return string
     */
    public static function getOs()
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (empty($ua)) {
            return 'android';
        }
        $wholeArr = self::convertAppUserAgentArr($ua);
        return !empty($wholeArr['OS']) ? strtolower($wholeArr['OS']) : 'android';
    }

    /**
     * @return string|null
     */
    public static function getPlatform()
    {
        $platform = self::PLATFORM_WAP;
        if (PHP_SAPI == 'cli') {
            $platform = self::PLATFORM_CLI;
        } else {
            $ua = self::getUserAgent();
            if ($ua) {
                if (preg_match("/WptMessenger\/([\d.]+)/i", $ua)) {
                    //微鉴宝
                    if (strpos($ua, 'wptpk/wjb') !== false) {
                        $platform = self::PLATFORM_WJB_APP;
                    } else {
                        $platform = self::PLATFORM_APP;
                    }
                } elseif (preg_match("/MicroMessenger\/([\d.]+)/i", $ua)) {
                    if (stripos($ua, 'WindowsWechat') !== false) {
                        $platform = self::PLATFORM_PC_WECHAT;
                    } else {
                        //获取平台
                        $defaultUserinfo = app('DefaultUserinfo');
                        $platform = get_property($defaultUserinfo, 'platform', '');
                        if ($platform != self::PLATFORM_WECHAT) {
                            //小程序
                            $platform = self::PLATFORM_WECHAT_APP;
                        }
                    }
                } elseif (!self::isMobile($ua)) {
                    $platform = self::PLATFORM_PC;
                } elseif (strpos(strtolower($ua), 'toutiaomicroapp') !== false) {
                    $platform = self::PLATFORM_DOUYIN;
                } elseif (strpos(strtolower($ua), 'swan/') !== false) {
                    $platform = self::PLATFORM_BAIDU_XCX;
                }
            }
        }
        return $platform;
    }

    /**
     * 判断是否是小程序 UA
     * @return bool
     * @author 丁圆圆
     */
    public static function isSmallProgram()
    {
        $userAgent = self::getUserAgent();

        if ($userAgent) {
            if (strpos($userAgent, "miniProgram") !== false) {
                return true;
            }

            //此处不区分大小写，抖音部分 app 大小写不同
            if (strpos(strtolower($userAgent), 'toutiaomicroapp') !== false) {
                return true;
            }

            //增加百度小程序判断
            if (strpos($userAgent, 'swan/') !== false) {
                return true;
            }
        }

        $referer = "";

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
        }

        if ($referer) {
            if (strpos($referer, 'miniProgram') !== false) {
                return true;
            }

            if ($userAgent && strpos(strtolower($userAgent), 'wxwork') !== false && strpos($referer, 'fr_wxworkmini') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 是否是微信小程序
     * @return bool
     * @author 丁圆圆
     */
    public static function isWechatSmallProgram()
    {
        $userAgent = self::getUserAgent();

        if ($userAgent) {
            if (strpos($userAgent, "miniProgram") !== false) {
                return true;
            }
        }

        $referer = "";

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
        }

        if ($referer) {
            if (strpos($referer, 'miniProgram') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 判断是否是抖音小程序\头条小程序
     * @return bool
     * @author 丁圆圆
     */
    public static function isDouYinSmallProgram()
    {
        $userAgent = self::getUserAgent();

        if ($userAgent) {
            //此处不区分大小写，抖音部分 app 大小写不同
            if (strpos(strtolower($userAgent), 'toutiaomicroapp') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 是否是百度小程序
     * @return bool
     */
    public static function isBaiDuSmallProgram()
    {
        $userAgent = self::getUserAgent();

        if ($userAgent) {
            if (strpos($userAgent, 'swan/') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $defaultRefer
     * @return mixed 优先返回url中指定的referer路径，其次返回本站中的上个页面路径，都没有的话，返回参数$defaultRefer指定的url
     */
    public static function getReferUrl($defaultRefer = "/")
    {
        $referer = Request::input('referer');
        if (empty($referer)) {
            $referer = Request::input('refer');
            if (empty($referer)) {
                if (!empty($_SERVER['HTTP_REFERER'])) {
                    $url = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
                    if (strpos(config('app.WEI_HOST'), $url)) {
                        $referer = $_SERVER['HTTP_REFERER'];
                    }
                }
                if (empty($referer)) {
                    $referer = $defaultRefer;
                }
            }
        }
        return $referer;
    }

    /**
     * 获取app版本
     * @param string $name
     * @return int
     */
    public static function getAppVersion($name = 'WptMessenger')
    {
        $version = 0;
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agentArr = self::convertAppUserAgentArr($_SERVER['HTTP_USER_AGENT']);

            if (isset($agentArr[$name])) {
                $version = (int)str_replace('.', '', $agentArr[$name]);
            }
        }
        return $version;
    }

    /**
     *  获取当前平台，将APP按照系统拆分
     * @return string|null
     */
    public static function getPlatformDetail()
    {
        $platform = self::getPlatform();
        if ($platform == 'app') {
            $platform = self::getOs();
        }
        return $platform;
    }

    /**
     * 获取ios系统版本号
     * @return string
     */
    public static function getiOSVersion()
    {
        $systemVersion = '';
        $userAgentStr = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (empty($userAgentStr)) {
            return $systemVersion;
        }
        //判断系统
        $agentArr = DeviceUtil::convertAppUserAgentArr($userAgentStr);
        $os = strtolower($agentArr['OS'] ?? ($agentArr['os'] ?? ''));
        if ($os != 'ios') {
            return $systemVersion;
        }

        if (!empty(get_property($agentArr, 'ver', ''))) {
            //获取系统版本号
            $systemVersion = $agentArr['ver'];
        } else {
            //判断系统版本号
            preg_match("/.*?\/ver\s+([\d.]+).*?/i", $userAgentStr, $matches);
            $systemVersionStr = $matches[1] ?? '';
            if (!empty($systemVersionStr)) {
                $systemVersionArr = explode('.', $systemVersionStr);
                $systemVersion = $systemVersionArr[0] ?? '';
            }
        }

        return $systemVersion;
    }

    /**
     * 判断是否是外挂头
     * @param string $targetAgent
     * @param string $channel
     * @param int $userinfoId
     * @return bool
     */
    public static function checkPlugInAgent($targetAgent = 'java/', $channel = 'like', $userinfoId = 0)
    {
        $userAgent = self::getUserAgent();
        if ($userAgent) {
            if (stripos(strtolower($userAgent), $targetAgent) !== false) {
                MLogger::info('checkPlugInAgent', '屏蔽外挂', ['channel' => $channel, 'userinfoId' => $userinfoId]);
                return true;
            }
        }

        return false;
    }


    /**
     * 检查用户是否有绑定手机号码 此方法 能不在业务中使用 就不要使用 通过路由中间件添加['checktel']
     * @param $telephone
     * @return bool
     * @throws ApiException
     */
    public static function hasBindTelephone($telephone)
    {
        //先检查app版本号 如果低于358则 不进行判断
        $appVersion = DeviceUtil::getAppVersion();
        if ($appVersion > 0 && $appVersion < 358) {
            return true;
        }

        //检查小程序版本号
        $xcxv = Request::input('xcxv');
        $userinfo = app('DefaultUserinfo');
        //如果客户端是小程序 且没有xcxv标识 则是老版本小程序 直接返回 20191227
        if (isset($userinfo->platform) && $userinfo->platform == 'wechatCx' && empty($xcxv)) {
            MLogger::info('CommonUtil', '老小程序版本不判断手机号', [
                'telephone' => $telephone,
                'xcxv' => $xcxv,
                'userinfoId' => $userinfo->userinfoId,
            ]);
            return true;
        }
        //老头条小程序兼容
        $ttxcxv = Request::input('ttxcxv');
        //如果客户端是头条小程序 且没有ttxcxv标识 则直接返回
        if (isset($userinfo->platform) && $userinfo->platform == 'dyCx' && empty($ttxcxv)) {
            MLogger::info('CommonUtil', '头条3系', [
                'telephone' => $telephone,
                'userinfoId' => $userinfo->userinfoId,
            ]);
            return true;
        }

        //检查手机号是否为空 TODO 校验手机绑定中间件改为新服务，$telephone变为bool
        if ($telephone === false || empty($telephone)) {
            throw new ApiException(ErrorConst::NO_BIND_TELEPHONE, ErrorConst::NO_BIND_TELEPHONE_MSG);
        }
        return false;
    }

    /**
     * 检查手机号,含国内和国际
     * @param $nationCode
     * @param $tel
     * @return bool
     */
    public static function checkMobile($nationCode, $tel)
    {
        app()->configure('mobile');
        $mobileArr = config('mobile');
        $codeArr = array_keys($mobileArr);
        if (!in_array($nationCode, $codeArr, true)) {
            return false;
        }

        $pattern = '/' . $mobileArr[$nationCode]['pattern'] . '/';
        if (!preg_match($pattern, $nationCode . $tel)) {
            return false;
        }
        return true;
    }

    /**
     * 格式化座机号码
     *
     * @param $txt
     * @return mixed
     */
    public static function formatMobile($txt)
    {
        if (strlen($txt) != 11) {
            return $txt;
        } else {
            return preg_replace('/(\d{3})-?(\d{4})-?(\d{1,4}).*/', "$1-$2-$3", $txt);
        }
    }

    /**
     * 获取当前用户手机号(国际)
     * @param $nationCode
     * @param $phoneNumber
     * @return string
     */
    public static function getCurrentMobile($nationCode, $phoneNumber)
    {
        if ($nationCode == 86) {
            return $phoneNumber;
        }
        return $nationCode . '-' . $phoneNumber;
    }
}
