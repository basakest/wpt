<?php
/**
 * 测试user model
 */

namespace App\Models;

use App\Utils\Singleton;

class UserModel extends BaseModel
{
    use Singleton;
    /**
     * xxxxxx
     *
     * @var string
     */
    protected $table = 'user';

    public static $allColumn = [
        'id',
        'username',
        'password'
    ];


}
