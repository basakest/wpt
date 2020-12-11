<?php


namespace App\Service\Example\Query;

class ExampleQuery
{
    /**
     * 可选取项
     */
    const SELECT_ABLES = [
        'id', 'type', 'delete_time', 'update_time', 'create_time', 'modify_time'
    ];


    /**
     * 可过滤项
     */
    const FILTER_ABLES = [
        'id'        => ['=', 'in', 'not in'],
        'delete_time'   => ['=', '<', '>', '<=', '>='],
        'create_time'   => ['=', '<', '>', '<=', '>='],
    ];

    /**
     * 可排序项
     */
    const ORDER_ABLES = [
        'id', 'create_time'
    ];
}