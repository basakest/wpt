<?php

namespace App\Models;

use App\Utils\Singleton;

class ImageModel extends BaseModel
{
    use Singleton;
    /**
     * xxxxxx
     *
     * @var string
     */
    protected $table = 'image';

    public static $allColumn = [
        'id',
        'title',
        'content'
    ];


}
