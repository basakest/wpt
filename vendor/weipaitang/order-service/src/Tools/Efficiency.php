<?php

namespace WptOrder\OrderService\Tools;

use App\Utils\CommonUtil;
use WptCommon\Library\Facades\MLogger;

/**
 * 效率工具
 *
 * Class Efficiency
 * @package WptOrder\OrderService\Tools
 */
class Efficiency
{
    /**
     * 快速过滤出订单字段和拍品字段
     * status和snapshot 要根据具体场景区分
     *
     * @param array $fields
     */
    public static function filterSaleAndOrderFields($fields = [])
    {
        $orderFileds = ['saleId', 'userinfoId', 'winUserinfoId', 'status',
            'dispute', 'disputeTime', 'isRated', 'unsoldReason', 'winJson',
            'delayPayTime', 'delayReceiptTime', 'paidTime', 'endTime',
            'deliveryTime', 'finishedTime', 'launchTime',
            'cTime', 'mTime', 'saleType', 'snapshot'];

        $snapshotFileds = ['uri', 'draftid', 'category', 'subCategory', 'openTime',
            'createTime', 'endTime', 'isShow', 'multiWins', 'pid', 'enableReturn',
            'expressFee', 'enableIdent', 'priceJson', 'profileJson', 'recommendTime'];

        $order = [];
        $sale = [];
        foreach ($fields as $field) {
            if ($field == 'id') {
                $order[] = 'saleId';
            } elseif ($field == 'type') {
                $order[] = 'saleType';
            } elseif (in_array($field, $orderFileds)) {
                $order[] = $field;
            } else {
                $sale[] = $field;
            }
            if (in_array($field, $snapshotFileds)) {
                $order[] = 'snapshot';
            }
        }

        echo "  \$orderFields = ['" . implode('\',\'', array_unique($order)) . "'];" . PHP_EOL .
            "  \$saleFields =  ['" . implode('\',\'', array_unique($sale)) . "'];" . PHP_EOL;
        exit();
    }
}