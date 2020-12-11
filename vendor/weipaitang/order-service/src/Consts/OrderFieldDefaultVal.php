<?php

namespace WptOrder\OrderService\Consts;


class OrderFieldDefaultVal
{
    const DEFAULT_VAL = [
        //'saleId'           => 0,
        //'userinfoId'       => 0,
        'winUserinfoId' => 0,
        //'status'           => 0,
        'dispute' => 0,
        'disputeTime' => 0,
        'isRated' => 0,
        'unsoldReason' => 0,
        'winJson' => '',
        'delayPayTime' => 0,
        'delayReceiptTime' => 0,
        'paidTime' => 0,
        //'endTime'          => 0,
        'deliveryTime' => 0,
        'finishedTime' => 0,
        'launchTime' => 0,
    ];
}