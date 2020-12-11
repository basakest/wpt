<?php


namespace App\Utils;

use App\ConstDir\CacheConst;
use App\Libraries\redlock\Redlock;
use Illuminate\Support\Facades\Config;
use WptUtils\Str;

class LockUtil
{
    /**
     * 取得锁ID
     *
     * @param int $len
     * @param string $pre
     * @return string
     */
    public static function getLockId($len = 6, $pre = '')
    {
        return $pre . Str::randString($len) . '_' . microtime(true);
    }

    /**
     * 锁操作
     *
     * @param $key
     * @param int $time
     * @return bool
     */
    public static function uriLock($key, $time = 180)
    {
        $_ttl = $time * 1000;
        $cacheKey = sprintf(CacheConst::URI_LOCKED, $key);

        // 新锁
        $redlocknew = new Redlock();
        $config = get_value(Config::get('redis'), 'redisDistributedLock' . (crc32($key) % 3), []);
        $servers = [
            [
                $config['host'],
                $config['port'],
                $config['timeout'],
                $config['password'],
            ],
        ];
        $redlocknew->init($servers, 0, 1);
        $newres = $redlocknew->lock($cacheKey, 1, $_ttl);
        $redlocknew->clear();

        return false === $newres;
    }

    //锁操作
    public static function lock($key, $time = 180)
    {
        $_ttl = $time * 1000;
        $cacheKey = sprintf(CacheConst::ACTIONLOCKED, $key);

        // 新锁
        $redlocknew = new Redlock();
        $config = get_value(Config::get('redis'), 'redisDistributedLock' . (crc32($key) % 3), []);
        $servers = [
            [
                $config['host'],
                $config['port'],
                $config['timeout'],
                $config['password'],
            ],
        ];
        $redlocknew->init($servers, 0, 1);
        $newres = $redlocknew->lock($cacheKey, 1, $_ttl);
        $redlocknew->clear();

        return false === $newres;
    }

    /**
     * 解锁操作
     *
     * @param $key
     * @return bool
     */
    public static function unlock($key)
    {
        $cacheKey = sprintf(CacheConst::ACTIONLOCKED, $key);

        // 新锁
        $redLock = new Redlock();
        $config = get_value(Config::get('redis'), 'redisDistributedLock' . (crc32($key) % 3), []);
        $servers = [
            [
                $config['host'],
                $config['port'],
                $config['timeout'],
                $config['password'],
            ],
        ];
        $redLock->init($servers, 0, 1);
        $redLock->unlock($cacheKey, 1);
        $redLock->clear();

        return true;
    }
}
