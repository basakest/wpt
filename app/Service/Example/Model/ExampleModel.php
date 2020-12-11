<?php
namespace App\Service\Example\Model;

use App\Foundation\Database\Model;

class ExampleModel extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'example';

    public $timestamps = false;

    const TYPE_STATE_OVER = 2;

    const TYPE_STATE_DELAY = 4;
}
