<?php

namespace App\Models;

use App\Utils\Singleton;

class CatrgoryModel extends BaseModel
{
    use Singleton;
    /**
     * xxxxxx
     *
     * @var string
     */
    protected $table = 'category';

    public static $allColumn = [
        'id',
        'title',
        'content'
    ];


}
