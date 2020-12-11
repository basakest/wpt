<?php


namespace WptUtils;

/**
 * Class TimeUtil
 * @package WptUtils
 */
class TimeUtil
{
    /**
     * 当前毫秒时间
     *
     * @return float
     */
    public static function getMillisecond()
    {
        return round(microtime(true) * 1000);
    }

    /**
     * @return int
     */
    public static function now(): int
    {
        return time();
    }

    public static function timeStyle($time, $leftMode = false)
    {
        $nowTM = time();
        if ($time > $nowTM) {
            $gtTime = $time - $nowTM;
            if ($gtTime < 60) {
                return '倒计时' . $gtTime . '秒';
            }
            if ($gtTime < 3600) {
                return intval($gtTime / 60) . '分钟';
            }
            if ($gtTime < 3600 * 24) {
                return intval($gtTime / 3600) . '小时';
            }
            if ($leftMode) {
                $leftDays = intval($gtTime / 86400);
                $leftHours = $gtTime - ($leftDays * 86400);
                return $leftDays . '天' . intval($leftHours / 3600) . '小时';
            } else {
                if ($gtTime >= 3600 * 24 && date("Y", $time) == date("Y", $nowTM)) {
                    return date("m-d H:i", $time);
                } else {
                    return date("y-m-d H:i", $time);
                }
            }
        }
        $ltTime = $nowTM - $time;
        if ($ltTime < 60) {
            return '刚刚';
        }
        if ($ltTime < 3600) {
            return intval($ltTime / 60) . '分钟前';
        }
        if ($ltTime < 3600 * 24) {
            return intval($ltTime / 3600) . '小时前';
        }
        if ($ltTime > 3600 * 24 && $ltTime < 3600 * 48) {
            return '1天前';
        }
        if ($ltTime > 3600 * 48 && date("Y", $time) == date("Y", $nowTM)) {
            return date("m-d H:i", $time);
        } else {
            return date("Y-m-d H:i", $time);
        }
    }

    /**
     * 是否是周五
     *
     * @return bool
     */
    public static function isFriday()
    {
        $nowTime = time();
        $thisWeekFriday = strtotime("friday this week");
        return ($nowTime > $thisWeekFriday && $nowTime < $thisWeekFriday + 86400) ? true : false;
    }

    /**
     * @param string $sign
     * @return array
     */
    public static function convertDateSignToUnix($sign = 'today'): array
    {
        switch ($sign) {
            case "today"://今天
                $startDate = mktime(0, 0, 0);
                $endDate = mktime(23, 59, 59);
                break;
            case "yesterday"://昨天
                $startDate = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
                $endDate = mktime(23, 59, 59, date('m'), date('d') - 1, date('Y'));
                break;
            case "last30"://最近30天
                $startDate = mktime(0, 0, 0, date('m'), date('d') - 30, date('Y'));
                $endDate = mktime(23, 59, 59, date('m'), date('d') - 1, date('Y'));
                break;
            case "lastWeek"://上周
                $startDate = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1 - 7, date('Y'));
                $endDate = mktime(23, 59, 59, date('m'), date('d') - date('w') + 7 - 7, date('Y'));
                break;
            case "lastMonth"://上月
                $startDate = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
                $endDate = mktime(23, 59, 59, date('m'), 1, date('Y')) - 24 * 3600;
                break;
            case "month"://本月
                $startDate = mktime(0, 0, 0, date('m'), 1, date('Y'));
                $endDate = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
                break;
            case "lastTwoWeek"://最近2周
                $startDate = mktime(0, 0, 0, date('m'), date('d') - 14, date('Y'));
                $endDate = mktime(23, 59, 59, date('m'), date('d') - 1, date('Y'));
                break;
            default://默认今天
                $startDate = mktime(0, 0, 0);
                $endDate = mktime(23, 59, 59);
                break;
        }
        return [$startDate, $endDate];
    }

    /**
     * 获取日期区间
     * @param $first string 开始时间(2019-07-01)
     * @param $last string 结束时间(2019-07-10)
     * @param string $step
     * @param string $format
     * @return array
     */
    public static function dateRange($first, $last, $format = 'Y-m-d', $step = '+1 day')
    {
        $dates = [];
        $current = strtotime($first);
        $last = strtotime($last);

        while ($current <= $last) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    /**
     * 获取 x 分钟下一场分钟,返回的是时间戳
     * 例子1： x = 20, 当前时间是：2019-11-12 20：15, 那么返回 2019-11-12  20：20
     * 例子2： x = 20, 当前时间是：2019-11-12 20：55, 那么返回 2019-11-12  21：00
     * @param $perMinute
     * @return false|int
     */
    public static function getNextMinute($perMinute)
    {
        $currentMinute = date('i');
        $div = (int)($currentMinute / $perMinute) + 1;
        $nextMinute = $div * $perMinute;
        $nextMinute = $nextMinute >= 60 ? 0 : $nextMinute;

        if ($nextMinute == 0) {
            return strtotime(date('Y-m-d H:0', strtotime('+1hours')));
        } else {
            return strtotime(date('Y-m-d H:' . $nextMinute));
        }
    }


    /**
     * 按照微信格式化列表日期样式(APP 3.5.9版本 使用)
     * @param int|string $sourceTime 需要格式化的时间戳 或 日期格式
     * @param bool $isList 显示位置是否是列表
     * @return false|mixed|string
     */
    public static function formatChatDateOld($sourceTime, $isList = true)
    {
        if (!$sourceTime) {
            return '';
        }
        if (!is_numeric($sourceTime)) {
            $sourceTime = strtotime($sourceTime);
        }
        $currentTime = time();
        $sourceDay = date('Ymd', $sourceTime);

        $weekday = date('N', $currentTime);
        $thisWeekStartTime = strtotime(date('Y-m-d', strtotime('-' . ($weekday - 1) . ' days')));
        $thisWeekEndTime = strtotime(date('Y-m-d', strtotime('+' . (8 - $weekday) . ' days')));

        $timeRangeText = self::getTimeRangeText($sourceTime);
        if (date('Ymd', $currentTime) == $sourceDay) {
            // 今天
            $result = date($timeRangeText . 'g:i', $sourceTime);
        } elseif (date('Ymd', strtotime('-1 day', $currentTime)) == $sourceDay) {
            // 昨天
            $result = $isList ? '昨天' : date('昨天 ' . $timeRangeText . 'g:i', $sourceTime);
        } elseif ($sourceTime >= $thisWeekStartTime && $sourceTime < $thisWeekEndTime) {
            // 本周
            $weekdays = [1 => '周一', 2 => '周二', 3 => '周三', 4 => '周四', 5 => '周五', 6 => '周六', 7 => '周日'];
            $day = date('N', $sourceTime);
            $sourceWeekday = $weekdays[$day] ?? '';
            $result = $isList ? $sourceWeekday : $sourceWeekday . ' ' . date('H:i');
        } elseif (date('Y', $currentTime) == date('Y', $sourceTime)) {
            // 本年
            $result = $isList ? date('n月j日', $sourceTime) : date('n月j日 ' . $timeRangeText . 'H:i', $sourceTime);
        } else {
            // 本年外
            $result = $isList ? date('Y年n月j日', $sourceTime) : date('Y年n月j日 ' . $timeRangeText . 'H:i', $sourceTime);
        }
        return $result;
    }

    /**
     * 获取时间段文本描述
     * @param int $time 要查询的时间戳
     * @return string
     */
    public static function getTimeRangeText($time)
    {
        $sourceHour24 = date('G', $time);
        $timeRangeText = '';
        if ($sourceHour24 <= 5) {
            $timeRangeText = '凌晨';
        } elseif ($sourceHour24 <= 11) {
            $timeRangeText = '早上';
        } elseif ($sourceHour24 <= 12) {
            $timeRangeText = '中午';
        } elseif ($sourceHour24 <= 17) {
            $timeRangeText = '下午';
        } elseif ($sourceHour24 <= 23) {
            $timeRangeText = '晚上';
        }
        return $timeRangeText;
    }

    /**
     * 按照微信格式化列表日期样式(APP 3.6.0及之后版本 使用)
     * @param int|string $sourceTime 需要格式化的时间戳 或 日期格式
     * @param bool $isList 显示位置是否是列表
     * @return false|mixed|string
     */
    public static function formatChatDate($sourceTime, $isList = true)
    {
        if (!$sourceTime) {
            return '';
        }
        if (!is_numeric($sourceTime)) {
            $sourceTime = strtotime($sourceTime);
        }
        $currentTime = time();
        $sourceDay = date('Ymd', $sourceTime);
        $beforeSenveDayTime = strtotime(date('Y-m-d', strtotime('-6 days')));

        if (date('Y', $currentTime) == date('Y', $sourceTime)) {
            // 本年
            if (date('Ymd', $currentTime) == $sourceDay) {
                // 今天 24小时格式HH:MM
                $result = date('H:i', $sourceTime);
            } elseif (date('Ymd', strtotime('-1 day', $currentTime)) == $sourceDay) {
                // 昨天
                $result = $isList ? '昨天' : date('昨天 H:i', $sourceTime);
            } elseif ($sourceTime >= $beforeSenveDayTime && $sourceTime <= $currentTime) {
                // 7天内，显示：星期X
                $weekdays = [1 => '星期一', 2 => '星期二', 3 => '星期三', 4 => '星期四', 5 => '星期五', 6 => '星期六', 7 => '星期日'];
                $day = date('N', $sourceTime);
                $weekday = $weekdays[$day] ?? '';
                $result = $isList ? $weekday : $weekday . ' ' . date('H:i', $sourceTime);
            } else {
                // 7天前
                $result = $isList ? date('Y/n/j', $sourceTime) : date('Y年n月j日 H:i', $sourceTime);
            }
        } else {
            // 本年外
            $result = $isList ? date('Y/n/j', $sourceTime) : date('Y年n月j日 H:i', $sourceTime);
        }
        return $result;
    }

    /**
     * 格式化时间格式
     * @param int|string $sourceTime 需要格式化的时间
     * @return string
     */
    public static function formatDateStyle($sourceTime)
    {
        if (!$sourceTime) {
            return '';
        }
        if (!is_numeric($sourceTime)) {
            $sourceTime = strtotime($sourceTime);
        }
        $currentTime = time();

        $diffTime = $currentTime - $sourceTime;
        if (date('Ymd', strtotime('-1 day', $currentTime)) == date('Ymd', $sourceTime)) {
            return '昨天';
        } elseif ($diffTime < 60) {
            return '刚刚';
        } elseif ($diffTime < 3600) {
            return intval($diffTime / 60) . '分钟前';
        } elseif ($diffTime < 3600 * 24) {
            return intval($diffTime / 3600) . '小时前';
        } elseif ($diffTime < 31536000) {
            return ceil($diffTime / 86400) . '天前';
        } elseif ($diffTime >= 31536000) {
            return intval($diffTime / 31536000) . '年前';
        } else {
            return '';
        }
    }
}
