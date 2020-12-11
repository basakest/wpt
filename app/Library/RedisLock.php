<?php
/**
 *
 * @auther heyu 2020/7/22
 */

namespace App\Library;

use App\Exceptions\GetLockException;
use Illuminate\Support\Facades\Redis;

class RedisLock
{
    private $key;

    private $expire = 3;

    const KV_REDIS_LOCK_KEY = 'Marketing_Redis_lock_key_%s';

    public function __construct($key, $expire = 3)
    {
        $this->key = $key;
        $this->expire = $expire;
    }

    /**
     * @throws GetLockException
     * @author heyu  2020/7/22 14:34
     */
    public function lock()
    {
        $redisKey = $this->getKey($this->key);
        $retry = 5;
        while ($retry--) {
            $ret = Redis::setnx($redisKey, 1);
            if ($ret === 1) {
                Redis::expire($redisKey, $this->expire);
                return;
            } else {
                usleep(10);
                continue;
            }
        }
        throw new GetLockException('获得锁超时');
    }

    public function unLock()
    {
        $redisKey = $this->getKey($this->key);
        Redis::del($redisKey);
    }

    private function getKey()
    {
        $arr = func_get_args();
        return sprintf(self::KV_REDIS_LOCK_KEY, $arr[0]);
    }

    public function getLock($key)
    {
        $lock = Redis::get(sprintf(self::KV_REDIS_LOCK_KEY, $key));
        return $lock;
    }
}
