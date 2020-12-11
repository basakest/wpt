<?php


namespace App\Foundation\Database;

use \Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{

    const DELETED_AT    = 'delete_time';
    const CREATED_AT    = 'create_time';
    const UPDATED_AT    = 'update_time';

    /**
     * 自动时间戳
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 模型的数据字段的保存格式。
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * 保护的字段
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 取消字段保护
     *
     * @var bool
     */
    protected static $unguarded = false;

}