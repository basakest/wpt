<?php


namespace App\Utils;

use App\ConstDir\CookieConst;
use App\ConstDir\RegularConst;
use App\Libraries\ci_encrypt\CI_Encrypt;
use Illuminate\Support\Facades\Cookie;
use WptUtils\Str;

/**
 * Class CookieUtil
 * @package App\Utils
 */
class CookieUtil
{
    /**
     * 设置wptCookie
     * @param string $name
     * @param $value
     * @param int $expire 过期时间
     */
    public static function setWptCookie($name, $value, $expire)
    {
        if ($expire == 0) {
            setcookie($name, $value, 0, '/', '.weipaitang.com');
        } else {
            setcookie($name, $value, time() + $expire, '/', '.weipaitang.com');
        }
    }

    /**
     * 删除wptCookie
     * @param string $name
     */
    public static function delWptCookie($name)
    {
        setcookie($name, '', time() - 1, '/', '.weipaitang.com');
    }

    /**
     * 取得userinfo cookie信息
     * @return bool|string
     */
    public static function getUserinfoCookie()
    {
        //如果是小程序请求则取小程序 cookie
        if (DeviceUtil::isSmallProgram()) {
            $cookieData = Cookie::get(CookieConst::USERINFOCX);
            if (empty($cookieData)) {
                //小程序没有指定cx-cookie 说明
                $cookieData = Cookie::get(CookieConst::USERINFO);
            }
        } else {
            $cookieData = Cookie::get(CookieConst::USERINFO);
        }

        if ($cookieData) {
            return json_decode((new CI_Encrypt())->decode($cookieData));
        }
        return false;
    }

    /**
     * 设置userinfo cookie 信息
     * @param $_cookie_data
     * @param int $_expire_time
     * @author 丁圆圆
     */
    public static function setUserinfoCookie($_cookie_data, $_expire_time = 86400)
    {
        $_cookie_data = (new CI_Encrypt())->encode(json_encode($_cookie_data));
        self::setWptCookie(CookieConst::USERINFO, $_cookie_data, $_expire_time);
    }

    /**
     * 设置小程序的 cookie 【此处微信，抖音，头条都使用这个cookie】
     * @param $cookieData
     * @param int $expireTime
     * @return string
     * @author 丁圆圆
     */
    public static function setUserInfoCx($cookieData, $expireTime = 86400)
    {
        $cookieData = (new CI_Encrypt())->encode(json_encode($cookieData));
        self::setWptCookie(CookieConst::USERINFOCX, $cookieData, $expireTime);
        return $cookieData;
    }

    /**
     * 获取浏览器唯一标识或者设置
     * @return string
     */
    public static function getSessionId()
    {
        $_cookieName = 'wptSessionId';
        $_sessionId = Cookie::get($_cookieName);

        if (!preg_match(RegularConst::SESSION_ID, $_sessionId)) {
            $_sessionId = false;
        }

        if (!$_sessionId) {
            $_sessionId = date('YmdHis') . '_' . Str::randString(10);
            self::setWptCookie($_cookieName, $_sessionId, 0);
        }
        return $_sessionId;
    }
}
