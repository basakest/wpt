<?php

namespace App\Utils;

use WptCommon\Library\Facades\MLogger;

class DiffArray
{
    /**
     * 默认忽略比较的字段，如须比较通过$mustcheck传参
     * @var array
     */
    private static $ignoreFields = [];

    /**
     * 包含以下字符串的key进行弱类型比较
     * @var array
     */
    private static $weakCompareTypeContains = ["userinfoId", "memberTime"];

    /**
     * 以以下字符串结尾的key进行弱类型比较
     * @var array
     */
    private static $weakCompareTypeEndswith = [];

    /**
     * 需要内容排序的字段
     * @var array
     */
    private static $sortFields = [];


    public static function checkAndLog(
        $oldData,
        $newData,
        $msg = 'checkAndLog',
        $onlyCheck = [],
        $mustCheck = [],
        $ignoreFields = [],
        $weakCompareTypeContains = []
    ) {
        if ($ignoreFields) {
            self::$ignoreFields = $ignoreFields;
        }
        if ($weakCompareTypeContains) {
            self::$weakCompareTypeContains = $weakCompareTypeContains;
        }

        try {
            $t2 = microtime(true);
            $content = [];
            $eq = self::check($newData, $oldData, $onlyCheck, $mustCheck, $content);
            $t3 = microtime(true);
            $checkTime = number_format(($t3 - $t2) * 1000, 4);
            $content['extend1'] = $checkTime;
            if ($eq) {
                MLogger::info("checkAndLog", $msg, $content, true);
            } else {
                MLogger::warning("checkAndLog", $msg, $content, true);
            }
        } catch (\Throwable $e) {
            MLogger::exception('checkAndLog', $e, $msg, []);
        }
        return;
    }

    /*
    private static function isEnvLocal()
    {
        return env('ENV', 'PROD') == 'LOCAL';
    }*/

    private static function check($newData, $oldData, $onlyCheck = [], $mustCheck = [], &$content = [])
    {
        $diff = [];
        if (is_numeric($newData) and is_numeric($oldData)) {
            $countEq = self::_compareCount($newData, $oldData, $diff);
            if (!$countEq) {
                $content = ['diff' => $diff, 'new_data' => $newData, 'old_data' => $oldData];
            } else {
                $content = ['new_data' => $newData, 'old_data' => $oldData];
            }
            return $countEq;
        }

        if (is_null($newData)) {
            $newData = [];
        }
        if (is_null($oldData)) {
            $oldData = [];
        }
        $contentEq = self::_compareContent($newData, $oldData, $onlyCheck, $mustCheck, $diff);
        //$countEq = self::_compareListCount($newData, $oldData, $diff);
        $countEq = true;

        if (($contentEq && $countEq) === false) {
            $newIds = self::_getIds($newData);
            $oldIds = self::_getIds($oldData);
            if (!empty($newIds) || !empty($oldIds)) {
                $content['ids_old'] = implode(',', $oldIds);
                $content['ids_new'] = implode(',', $newIds);
            }

            $content = ((array)$content) + [
                    'diff' => $diff,
                    'old_data' => count($oldData) > 60 ? ['...'] : $oldData,
                    'new_data' => count($newData) > 60 ? ['...'] : $newData
                ];
        } else {
            $content = ['new_data' => $newData, 'old_data' => $oldData];
        }
        return $contentEq && $countEq;
    }

    /*
    private static function _compareListCount($newData, $oldData, &$diff = [])
    {
        if (!is_array($newData) || !is_array($oldData)) {
            return true;
        }
        $eq = count($newData) == count($oldData);
        if ($eq) {
            return true;
        }
        $diff[] = ['列表数量', count($oldData), count($newData)];
        return $eq;
    }*/

    private static function _getIds($list)
    {
        $new = self::_getPluck($list, 'id');
        if (!empty($new)) {
            return $new;
        }
        return [];
    }

    private static function _getPluck($list, $filed)
    {
        if (empty($list)) {
            return [];
        }
        $obj = reset($list);
        if (!is_array($obj) && !is_object($obj)) {
            return [];
        }
        $obj = (array)$obj;
        if (!isset($obj[$filed])) {
            return [];
        }
        return array_pluck($list, $filed);
    }

    private static function _listKeyBy($list)
    {
        if (!is_array($list)) {
            return $list;
        }
        if (empty($list)) {
            return $list;
        }
        if (!is_object(reset($list))) {
            return $list;
        }
        if (!(reset($list)->id ?? false)) {
            return $list;
        }
        return collect($list)->keyBy('id')->toArray();
    }

    private static function _compareCount($newData, $oldData, &$diff = [])
    {
        $countEq = $newData == $oldData;
        if (!$countEq) {
            $diff[] = ['数量', $oldData, $newData];
        }
        return $countEq;
    }

    private static function _compareContent($newData, $oldData, $onlyCheck = [], $mustCheck = [], &$diff = [])
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
            if ($onlyCheck && !str_contains($k, $onlyCheck)) {
                continue;
            }
            if (!str_contains($k, $mustCheck) && str_contains($k, self::$ignoreFields)) {
                continue;
            }

            $n = $newDataDot[$k] ?? null;

            if (str_contains($k, ["headimgurl", "headImage"])) {
                if ($v) {
                    $v = str_replace("http://", "https://", $v);
                }
                if ($n) {
                    $n = str_replace("http://", "https://", $n);
                }
            }
            if (str_contains($k, "memberLevelScores")) {
                continue;
            }

            if (!self::_compareValue($k, $v, $n)) {
                $diff[] = [$k, '(' . gettype($v) . ')' . (is_array($v) ? json_encode($v) : $v), '(' . gettype($n) . ')' . ($n ?? "null")];
            }
        }
        return empty($diff);
    }

    /**
     * 对特定字段的内容进行排序
     * @param $data
     * @return array|mixed
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
        if (!is_object($obj)) {
            return $obj;
        }
        foreach (self::$sortFields as $field) {
            if (isset($obj->$field) && is_array($obj->$field) && !empty($obj->$field)) {
                sort($obj->$field);
            }
        }
        return $obj;
    }

    /**
     * 部分字段忽略类型比较，其他字段严格比较
     * @param $k
     * @param $v1
     * @param $v2
     * @return bool
     */
    private static function _compareValue($k, $v1, $v2)
    {
        if ($v1 === $v2) {
            return true;
        }
        if (str_contains($k, self::$weakCompareTypeContains) && $v1 == $v2) {
            return true;
        }
        if (ends_with($k, self::$weakCompareTypeEndswith) && $v1 == $v2) {
            return true;
        }
        return false;
    }
}
