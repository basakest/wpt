<?php

namespace App\Logic\User;

use App\Exceptions\ApiException;
use App\Models\UserModel;
use App\Utils\Singleton;

class UserLogic
{
    use Singleton;

    public function nameHasUsed($username)
    {
        return UserModel::getInstance()->getOne('id', ['username' => $username]);
    }

    public function notRegister($username)
    {
        return !UserModel::getInstance()->getOne('id', ['username' => $username]);
    }

    public function register($username, $password)
    {
        if ($this->getLoginUserId()) {
            throw new ApiException('该用户已经登录', 1);
        }
        if ($this->nameHasUsed($username)) {
            throw new ApiException('用户名已经被使用', '1');
        }
        return UserModel::getInstance()->insertData(['username' => $username, 'passwordHash' => password_hash($password, PASSWORD_DEFAULT)]);
    }

    public function login($username, $password)
    {
        if ($this->notRegister($username)) {
            throw new ApiException('该用户没有注册', 1);
        }
        if ($this->getLoginUserId()) {
            throw new ApiException('该用户已经登录', 1);
        }
        $password_hash = UserModel::getInstance()->getOne('passwordHash', ['username' => $username])->passwordHash;
        if (password_verify($password, $password_hash)) {
            $id = $this->getUserId($username);
            $hashed_id = base64_encode($id);
            setcookie('hashed_id', $hashed_id, time() + 60 * 60 * 24);
            return ['username' => $username];
            //异常的方式不能添加data属性
            //throw new ApiException('注册成功', '1');
            //json_encode添加了不必要的双引号
            // array
            //return json_encode(['code' => 1, 'msg' => '成功'], JSON_UNESCAPED_UNICODE);
            // object
            //$res = new \stdClass();
            //$res->code = 1;
            //$res->message = "登录成功";
            //return \GuzzleHttp\json_encode($res);
        } else {
            throw new ApiException('用户名与密码不匹配', '0');
        }
    }

    public function getUserId($username)
    {
        return UserModel::getInstance()->getOne('id', ['username' => $username])->id;
    }

    public function getLoginUserId()
    {
        if (isset($_COOKIE['hashed_id'])) {
            $hashed_id = $_COOKIE['hashed_id'];
            return base64_decode($hashed_id);
        } else {
            return 0;
        }
    }

    public function logout()
    {
        $id = $this->getLoginUserId();
        if ($id) {
            setcookie('hashed_id', '', time() - 60);
            return '';
        } else {
            throw new ApiException('你尚未登录，无法退出', 0);
        }
    }

    public function idToName($id)
    {
        return UserModel::getInstance()->getOne(['username'], ['id' => $id])->username;
    }
}