<?php

namespace App\Models;

use App\Utils\Singleton;

class ArticleModel extends BaseModel
{
    use Singleton;
    /**
     * xxxxxx
     *
     * @var string
     */
    protected $table = 'article';

    public static $allColumn = [
        'id',
        'title',
        'content'
    ];


}
