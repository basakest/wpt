<?php

namespace WptCommon\Library\Consts;

class AlertTypes
{
    // 钉钉报警
    const ALERT_DING = 'dingding';
    // 邮件报警
    const ALERT_MAIL = 'mail';

    public static function check($type)
    {
        if (empty($type)) {
            return true;
        }

        $types = explode('|', $type);
        $objClass = new \ReflectionClass(self::class);
        $allowTypes = $objClass->getConstants();
        $diffTypes = array_diff($types, $allowTypes);
        if (!empty($diffTypes)) {
            throw new \RuntimeException('报警类型[' . implode('|', $diffTypes) . ']不合法,目前仅支持:' . implode('|', $allowTypes));
        }
        return true;
    }
}
