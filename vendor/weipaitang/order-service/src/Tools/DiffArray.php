<?php

namespace WptOrder\OrderService\Tools;

use App\Utils\CommonUtil;
use WptCommon\Library\Facades\MLogger;

class DiffArray
{
    /**
     * 默认忽略比较的字段，如须比较通过$mustcheck传参
     * @var array
     */
    private static $ignoreFields = ['profileJson', 'profile.', 'winJson', 'priceJson', 'systemBzjJson', 'handicraft', 'views', 'likes'];

    /**
     * 包含以下字符串的key进行弱类型比较
     * @var array
     */
    private static $weakCompareTypeContains = ['systemBzj'];

    /**
     * 以以下字符串结尾的key进行弱类型比较
     * @var array
     */
    private static $weakCompareTypeEndswith = ['win'];

    /**
     * 需要内容排序的字段
     * @var array
     */
    private static $sortFields = ['systemBzj'];

    /**
     * @param $grayTag
     * @param $oldData
     * @param \Closure $callback
     * @param string $msg
     * @param array $mustCheck
     * @return bool|void
     */
    public static function transfer($grayTag, &$oldData, \Closure $callback, $msg = '', $mustCheck = [])
    {
        $isGray = self::_isGray($grayTag);
        if (!$isGray) return;
        try {
            $t1 = microtime(true);
            $newData = $callback();
            $t2 = microtime(true);
            $content = [];
            $eq = self::check($newData, $oldData, $mustCheck, $content);
            $t3 = microtime(true);

            $runtime = number_format(($t2 - $t1) * 1000, 4);
            $checktime = number_format(($t3 - $t2) * 1000, 4);
            $alltime = number_format(($t3 - $t1) * 1000, 4);
            $timeDesc = "runtime:[$runtime ms] checktime[$checktime ms] alltime[$alltime]";
            $content['extend1'] = $runtime;
            $content['extend2'] = $checktime;
            $content['extend3'] = $alltime;
            if ($eq) {
                MLogger::info("order-service-2", "check data success! $timeDesc : $msg", $content, true);
            } else {
                MLogger::warning("order-service-2", "check data fail! $timeDesc : $msg", $content, true);
            }

            if (self::isEnvTest()) {
                MLogger::info("order-service-2", 'using new data : ' . $msg);
                $oldData = $newData;
            }

            return $eq;
        } catch (\Throwable $e) {
            if (self::isEnvLocal()) throw $e;
            MLogger::exception('order-service-2', $e, '', []);
        }
        return;
    }

    private static function isEnvTest()
    {
        return in_array(env('ENV', 'PROD'), ['LOCAL', 'TEST']);
    }

    private static function isEnvLocal()
    {
        return env('ENV', 'PROD') == 'LOCAL';
    }

    private static function check($newData, $oldData, $mustCheck = [], &$content = [])
    {
        if (is_numeric($newData) and is_numeric($oldData)) {
            $countEq = self::_compareCount($newData, $oldData, $diff);
            if (!$countEq) {
                $content = ['diff' => $diff, 'new_data' => $newData, 'old_data' => $oldData];
            } else {
                $content = ['new_data' => $newData, 'old_data' => $oldData];
            }
            return $countEq;
        }

        if (is_null($newData)) $newData = [];
        if (is_null($oldData)) $oldData = [];
        $contentEq = self::_compareContent($newData, $oldData, $mustCheck, $diff);
        $countEq = self::_compareListCount($newData, $oldData, $countDiff);

        if (($contentEq && $countEq) === false) {
            $newIds = self::_getIds($newData);
            $oldIds = self::_getIds($oldData);
            if (!empty($newIds) || !empty($oldIds)) {
                $content['ids_old'] = implode(',', $oldIds);
                $content['ids_new'] = implode(',', $newIds);
                self::_printDebugInfo([['ID列表', "[{$content['ids_old']}]", "[{$content['ids_new']}]"]]);
            }

            $content = ((array)$content) + ['diff' => $diff,
                    'old_data' => count($oldData) > 50 ? ['...'] : $oldData,
                    'new_data' => count($newData) > 50 ? ['...'] : $newData];
        } else {
            $content = ['new_data' => $newData, 'old_data' => $oldData];
        }
        return $contentEq && $countEq;
    }

    private static function _isGray($grayTag)
    {
        if (self::isEnvTest()) return true;
        return CommonUtil::isOrderGrayUserByTag($grayTag, random_int(0, 100));
    }

    private static function _compareListCount($newData, $oldData, &$diff = [])
    {
        if (!is_array($newData) || !is_array($oldData)) return true;
        $eq = count($newData) == count($oldData);
        if ($eq) return true;
        $diff[] = ['列表数量', count($oldData), count($newData)];
        self::_printDebugInfo($diff, $newData, $oldData);
        return $eq;
    }

    private static function _getIds($list)
    {
        $fileds = ['id', 'saleId'];
        foreach ($fileds as $filed) {
            $new = self::_getPluck($list, 'id');
            if (!empty($new)) return $new;
        }
        return [];
    }

    private static function _getPluck($list, $filed)
    {
        if (empty($list)) return [];
        $obj = reset($list);
        if (!is_array($obj) && !is_object($obj)) return [];
        $obj = (array)$obj;
        if (!isset($obj[$filed])) return [];
        return array_pluck($list, $filed);
    }

    private static function _listKeyBy($list)
    {
        if (!is_array($list)) return $list;
        if (empty($list)) return $list;
        if (!is_object(reset($list))) return $list;
        if (!(reset($list)->id ?? false)) return $list;
        return collect($list)->keyBy('id')->toArray();
    }

    private static function _compareCount($newData, $oldData, &$diff = [])
    {
        $countEq = $newData == $oldData;
        if (!$countEq) {
            $diff[] = ['数量', $oldData, $newData];
        }
        self::_printDebugInfo($diff, $newData, $oldData);
        return $countEq;
    }

    private static function _compareContent($newData, $oldData, $mustCheck = [], &$diff = [])
    {
        $diff = [];
        if (gettype($newData) != gettype($oldData)) {
            $diff[] = ['类型', '(' . gettype($oldData) . ')', '(' . gettype($newData) . ')'];
        }
        $newData = self::_listKeyBy(self::sortFieldContent($newData));
        $oldData = self::_listKeyBy(self::sortFieldContent($oldData));
        $newDataDot = array_dot(objectToArray($newData));
        $oldDataDot = array_dot(objectToArray($oldData));

        foreach ($oldDataDot as $k => $v) {
            if (!str_contains($k, $mustCheck) && str_contains($k, self::$ignoreFields)) {
                continue;
            }
            $n = $newDataDot[$k] ?? null;
            if (!self::_compareValue($k, $v, $n)) {
                $diff[] = [$k, '(' . gettype($v) . ')' . $v, '(' . gettype($n) . ')' . $n];
            }
        }
        self::_printDebugInfo($diff, $newData, $oldData);
        return empty($diff);
    }

    /**
     * 对特定字段的内容进行排序
     * @param $data
     */
    private static function sortFieldContent($data)
    {
        if (is_object($data)) {
            $arr = (array)$data;
            if (count(array_filter(array_keys($arr), 'is_numeric')) == count($arr)) {
                $data = $arr;
            } else {
                return self::sortObjField($data);
            }
        }

        if (is_array($data)) {
            foreach ($data as $k => $item) {
                $data[$k] = self::sortObjField($item);
            }
        }
        return $data;
    }

    private static function sortObjField($obj)
    {
        if (!is_object($obj)) return;
        foreach (self::$sortFields as $field) {
            if (isset($obj->$field) && is_array($obj->$field) && !empty($obj->$field)) {
                sort($obj->$field);
            }
        }
        return $obj;
    }


    /**
     * 部分字段忽略类型比较，其他字段严格比较
     * @param $v1
     * @param $v2
     * @return bool
     */
    private static function _compareValue($k, $v1, $v2)
    {
        if ($v1 === $v2) return true;
        if (str_contains($k, self::$weakCompareTypeContains) && $v1 == $v2) return true;
        if (ends_with($k, self::$weakCompareTypeEndswith) && $v1 == $v2) {
            return true;
        }
        return false;
    }

    private static function _printDebugInfo($diff)
    {
        if (!self::isEnvLocal()) return;
        $isEq = empty($diff);
        self::_output('');
        if ($diff) {
            self::_output('');
            self::_output('--------------------------------------');
            self::_output("[ ]  字段     老数据     新数据");
        }
        collect($diff)->each(function ($item) {
            self::_output(implode('  ', $item), 'red');
        });

        $diff and self::_output('--------------------------------------');
    }

    /**
     * @param $msg
     * @param string $color 暂时只支持红色标记
     */
    private static function _output($msg, $color = '')
    {
        $isConsle = (app()->runningInConsole() or app()->runningUnitTests());
        $eol = $isConsle ? PHP_EOL : '<br>';
        $msg = !empty($color) ? ($isConsle ? "\e[0;31m[x]$msg\e[0m" : "<span style='color: red'>$msg</span>") : $msg;
        if (is_string($msg)) {
            echo $msg . $eol;
        } else {
            var_dump($msg);
        }
    }
}