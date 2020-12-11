<?php


namespace App\Utils;

use App\CodisCache\UseLessCodisCache;
use App\ConstDir\BaseConst;
use Illuminate\Support\Facades\Request;
use WptCommon\Library\Facades\MLogger;

class IpUtil
{
    public static function forbidIp($channel, $visitTime, $maxNum, $punishTimeStamp, $userinfoId = 0, $customIp = '')
    {
        $ip = !empty($customIp) ? $customIp : Request::ip();

        if (!empty($ip) && !in_array($ip, BaseConst::GOOD_IP)
            &&
            strpos($ip, '62.234.63') === false && strpos($ip, '62.234.195') === false && strpos($ip, '118.126.83') === false &&
            strpos($ip, '212.64.123') === false && strpos($ip, '111.230.122') === false && strpos($ip, '129.204.26') === false &&
            strpos($ip, '129.204.191') === false && strpos($ip, '148.70.255') === false && strpos($ip, '129.28.63') === false &&
            strpos($ip, '124.156.191') === false && strpos($ip, '212.64.117.183') === false && strpos($ip, '111.231.130.148') === false &&
            strpos($ip, '49.234.112') === false
        ) {
            $forbidIpChannel = UseLessCodisCache::getForbidIpChannel($channel, $ip);
            if ($forbidIpChannel !== false) {
                //REFERER 是主站的，放出，不屏蔽
                if (!empty($_SERVER['HTTP_REFERER'])) {
                    $url = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
                    if (strpos(config('app.WEI_HOST'), $url)) {
                        return false;
                    }
                }

                MLogger::info(
                    'ForbidMan',
                    $channel . '_1',
                    ['userinfoId' => $userinfoId, 'ip' => $ip, 'time' => date('Y-m-d H:i:s'), 'timeline' => time()]
                );
                UseLessCodisCache::setForbidMan($userinfoId, time());
                return true;
            }

            $badNum = UseLessCodisCache::incrChannelBadIpNum($channel, $ip, 1, $visitTime);
            if ($badNum > $maxNum) {
                //防错，当超过封的时候，删除。
                UseLessCodisCache::delChannelBadIpNum($channel, $ip);

                $nowTime = time();
                MLogger::info(
                    'ForbidMan',
                    $channel . '_2',
                    ['userinfoId' => $userinfoId, 'ip' => $ip, 'time' => date('Y-m-d H:i:s'), 'timeline' => $nowTime]
                );
                UseLessCodisCache::setForbidIpChannel($channel, $ip, $nowTime, $punishTimeStamp);
            }
        }
        return false;
    }

    /**
     * 获取真实Ip
     * @return mixed|string
     */
    public static function getRealIp()
    {
        $list = [
            '111.231.113.248',
            '115.159.180.164',
            '182.254.247.147',
            '118.89.149.228',
            '115.159.201.233',
            '115.159.74.147',
            '115.159.180.131',
            '111.231.66.17',
            '211.159.217.236',
            '182.254.210.29',
            '115.159.36.232',
            '123.206.206.246',
            '182.254.217.28',
            '123.206.207.123',
            '111.231.102.69',
            '118.89.114.217',
            '122.152.196.132',
            '115.159.209.178',
            '123.206.193.186',
            '123.206.225.88',
            '115.159.122.33',
            '123.206.216.128',
            '115.159.144.192',
            '182.254.217.42',
            '122.152.198.116',
            '115.159.93.78',
            '115.159.116.79',
            '115.159.126.185',
            '123.206.205.161',
            '115.159.71.112',
            '123.206.107.139',
            '122.152.197.50',
            '123.206.113.179',
            '123.206.134.89',
        ];

        $reqIp = Request::ip();

        $inWhiteList = false;
        if (in_array($reqIp, $list)) {
            $inWhiteList = true;
        }

        $list1 = [
            '49.234.112',
            '118.126.83',
            '212.64.123'
        ];

        $newIp = explode('.', $reqIp);
        array_pop($newIp);
        $newIp = implode(".", $newIp);

        if (in_array($newIp, $list1)) {
            $inWhiteList = true;
        }

        if ($inWhiteList) {
            $ipStr = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
            $xffIp = null;
            if (!empty($ipStr)) {
                $ipArr = explode(',', $ipStr);
                $xffIp = $ipArr[0] ?? '';
            }
            $ip = !empty($xffIp) ? $xffIp : $reqIp;
            return $ip;
        }

        return $reqIp;
    }
}
