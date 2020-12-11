<?php


namespace WptBus\Lib;

use Closure;
use WptCommon\Library\Facades\MLogger;

class DataCompare
{
    /**
     * 日志文件名
     * @var string
     */
    private $logfile = 'data-compare';

    /**
     * niffler-project配置
     * @var string
     */
    private $nifflerProjectName = 'api.weipaitang.com';

    /**
     * niffler-key配置
     * @var string
     */
    private $nifflerKey = 'bus-data-compare';


    /**
     * 需要跳过比较的字段名
     * @var array
     */
    private $skip = [];

    /**
     * 额外信息
     * @var string|array
     */
    private $extraMsg = '';

    public static function getInstance()
    {
        return new self();
    }

    /**
     * ab，非a即b，用于接口改版线上切换
     * @param string $tag
     * @param Closure $a
     * @param Closure $b
     * @return mixed
     */
    public function ab(string $tag, Closure $a, Closure $b)
    {
        $this->extraMsg($tag);
        $ab = $this->getNifflerConfig($tag, 'ab', 0);
        if ($ab === 1) {
            $this->info('ab enter [b]');
            return $b();
        }
        return $a();
    }

    /**
     * 灰度百分比上线，配置百分比进行上线
     * @param string $tag
     * @param Closure $a
     * @param Closure $b
     * @return mixed
     */
    public function grayPresent(string $tag, Closure $a, Closure $b, $extraMsg = '')
    {
        $this->extraMsg($extraMsg ?: $tag);
        $isGray = $this->isGray($tag);
        if ($isGray) {
            $this->info("gray present enter [new]");
            return $b();
        }
        // $this->info("gray present enter [old]");
        return $a();
    }

    /**
     * @param string $tag
     * @param int $userId
     * @param Closure $a
     * @param Closure $b
     * @param string $extraMsg
     * @return mixed
     */

    public function whiteList(string $tag, int $userId, Closure $a, Closure $b, $extraMsg='') {
        $this->extraMsg($extraMsg ?: $tag);
        if ($this->isWhiteList($tag, $userId) || $this->isGray($tag)) {  // 在白名单或者百分比
            $this->info("gray present enter [new]",[$tag, $userId]);
            return $b();
        }
        return $a();
    }

    /**
     * 灰测通过比例
     * @param string $tag
     * @param string $key
     * @param int $mark 0-100
     * @return bool
     */
    public function grayByInRatio(string $tag, string $key, $mark = 0)
    {
        if ($this->isEnvTest()) {
            return true;
        }

        if (empty($mark)) {
            return false;
        }
        if (!is_numeric($mark)) {
            $mark = base_convert(bin2hex(substr($mark, -5)), 16, 10);
        }
        $result = $this->getNifflerConfigByTagAndKey($tag, $key, []);
        if (in_array($mark, $result['mark'] ?? [])) {
            return true;
        }
        $markRate = $mark % 100;
        $rate = $result['rate'] ?? 0;
        return $markRate < $rate;
    }

    private function getNifflerConfigByTagAndKey($tag, $key, $default = null)
    {
        try {
            $ret = app('NifflerConfig')->getConfig($this->nifflerProjectName, $tag);
            if ($ret->success && !empty($ret->data)) {
                $config = json_decode($ret->data, true);
                return $config[$key] ?? $default;
            }
            return $default;
        } catch (\Throwable $e) {
            $this->exception($e);
            return $default;
        }
    }

    /**
     * 数据比对
     * @param $grayTag
     * @param array|object|null|Closure|bool|mixed $oldData
     * @param Closure $callback
     * @param string|array $extraMsg
     * @return bool|void
     */
    public function handle($grayTag, $oldData, Closure $callback, $extraMsg = '')
    {
        try {
            $isGray = $this->isGray($grayTag);
            if (!$isGray) {
                return;
            }
            $this->extraMsg($extraMsg ?: $grayTag);
            $eq = $this->benchmark($callback, $oldData);
            return (bool)$eq;
        } catch (\Throwable $e) {
            $this->exception($e);
        }
        return;
    }

    /**
     * 递归比对数据
     * @param $new
     * @param $old
     * @param array $keyPath
     * @return bool
     */
    public function checkData($new, $old, $keyPath = [])
    {
        $typeOld = gettype($old);
        $typeNew = gettype($new);

        if ($this->isSkip($keyPath)) {
            return true;
        }

        if (!in_array($typeOld, ['array', 'object'])) {
            $eq = $new === $old;
            if (!$eq) {
                $diff = ['new' => $new, 'old' => $old, 'key' => implode('.', $keyPath)];
                $this->warn("value diff", ['diff' => $diff, 'new' => $new, 'old' => $old]);
            }
            return $eq;
        }

        if ($typeOld !== $typeNew) {
            $diff = ['new' => $typeNew, 'old' => $typeOld, 'key' => implode('.', $keyPath)];
            $this->warn("type diff", ['diff' => $diff, 'new' => $new, 'old' => $old]);
            return false;
        }

        $eq = true;
        foreach ($old as $key => $item) {
            $value = is_array($new) ? ($new[$key] ?? null) : ($new->$key ?? null);
            $eq &= $this->checkData($value, $item, array_merge($keyPath, [$key]));
        }
        return $eq;
    }


    /**
     * 记录性能数据到日志
     * @param Closure $callback
     * @param $oldData
     * @return bool
     */
    private function benchmark(Closure $callback, $oldData)
    {
        $t1 = microtime(true);
        if ($oldData instanceof Closure) {
            $oldData = $oldData();
        }
        $newData = $callback();
        $t2 = microtime(true);
        $content = [];
        $eq = $this->checkData($newData, $oldData);
        $t3 = microtime(true);

        $runtime = number_format(($t2 - $t1) * 1000, 4);
        $checktime = number_format(($t3 - $t2) * 1000, 4);
        $alltime = number_format(($t3 - $t1) * 1000, 4);
        $content['extend1'] = $runtime;
        $content['extend2'] = $checktime;
        $content['extend3'] = $alltime;

        $timeDesc = "runtime:[$runtime ms] checktime[$checktime ms] alltime[$alltime ms]";

        $content['new'] = $newData;
        $content['old'] = $oldData;
        if ($eq) {
            $this->info("compare pass! $timeDesc", $content);
        } else {
            $this->warn("compare fail! $timeDesc", $content);
        }
        return $eq;
    }

    public function extraMsg($extraMsg)
    {
        $this->extraMsg = $extraMsg;
        return $this;
    }

    /**
     * 设置跳过比对的字段
     * @param array $skip
     * @return $this
     */
    public function skip(array $skip)
    {
        $this->skip = $skip;
        return $this;
    }


    private function isSkip($keyPath)
    {
        $haystack = implode('.', $keyPath);
        foreach ($this->skip as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    private function getNifflerConfig($tag, $key, $default = null)
    {
        try {
            $ret = app('NifflerConfig')->getConfig($this->nifflerProjectName, $this->nifflerKey);
            if ($ret->success && !empty($ret->data)) {
                $config = json_decode($ret->data, true);
                return $config[$tag][$key] ?? $default;
            }
            return $default;
        } catch (\Throwable $e) {
            $this->exception($e);
            return $default;
        }
    }

    public function isWhiteList ($tag, $userId)
    {
        $ret = $this->getNifflerConfig($tag, 'whiteList', []);

        if (in_array($userId, $ret)) {
            return true;
        }
        return false;
    }

    public function isGray($tag)
    {
        // if ($this->isEnvTest()) {
        //     return true;
        // }
        $percent = $this->getNifflerConfig($tag, 'percent', 0);
        if (random_int(1, 100) <= $percent) {
            return true;
        }
        return false;
    }

    private function isEnvTest()
    {
        return in_array(env('ENV', 'PROD'), ['LOCAL', 'TEST']);
    }


    private function info($msg, $content = [])
    {
        $content['extraMsg'] = $this->extraMsg;
        $this->console($msg . ' => ' . json_encode($content ?? [], JSON_UNESCAPED_UNICODE), 'green');
        Log::info($this->logfile, $msg, $content);
    }

    private function warn($msg, $content = [])
    {
        $content['extraMsg'] = $this->extraMsg;
        $this->console($msg . ' => ' . json_encode($content ?? [], JSON_UNESCAPED_UNICODE), 'red');
        Log::warning($this->logfile, $msg, $content);
    }

    private function exception(\Throwable $e)
    {
        $this->console($e->getMessage(), 'red');
        $content = [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'detail' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'extraMsg' => $this->extraMsg
        ];
        Log::warning($this->logfile, $e->getMessage(), $content);
    }

    /**
     * 调试时控制台输出
     * @param $msg
     * @param string $color 支持green、red
     */
    private function console($msg, $color = '')
    {
        $isConsle = (app()->runningInConsole() or app()->runningUnitTests());
        if (!$isConsle) {
            return;
        }
        if (!$this->isEnvTest()) {
            return;
        }
        switch ($color) {
            case 'red':
                $msg = "\e[0;31m[x]$msg\e[0m";
                break;
            case 'green':
                $msg = "\e[0;32m[√]$msg\e[0m";
                break;
            default;
        }
        if (is_string($msg)) {
            echo PHP_EOL . $msg;
        } else {
            var_dump($msg);
        }
    }
}
