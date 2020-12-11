<?php


namespace WptDataCenter\Concerns;


use InvalidArgumentException;

trait DataCenterTraits
{
    /**
     * 当前时间往前推几天前 时间区间
     * e.g. DataCenter::getInstance()->dayRange(3, 4)->getxxxxx() 3天前 4天前 共2天的数据
     * e.g. DataCenter::getInstance()->dayRange(3, 5)->getxxxxx() 3天前 4天前 5天前 共3天的数据
     *
     * @param int $start
     * @param int $end
     * @return $this
     */
    public function dayRange(int $start, int $end)
    {
        if ($start < 0 || $end < 0) {
            throw new InvalidArgumentException("时间区间错误");
        }
        $start = min($start, $end);
        $end = max($start, $end);

        for ($i = $start; $i <= $end; $i++) {
            $this->dayRange[] = date("Ymd", strtotime(-1 * $i . ' day'));
        }
        sort($this->dayRange);
        return $this;
    }

    /**
     * 今天的数据
     * @return $this
     */
    public function today()
    {
        $this->dayRange[] = date("Ymd");
        return $this;
    }

    /**
     * 昨天的数据
     * @return $this
     */
    public function yestoday()
    {
        $this->dayRange[] = date("Ymd", strtotime("-1 day"));
        return $this;
    }
}
