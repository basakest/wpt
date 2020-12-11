<?php

namespace App\Http\Api\Controllers;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Logic\User\UserLogic;
use Illuminate\Http\Request;
use App\Logic\User\ArticleLogic;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        if (empty($username)) {
            throw new ApiException("用户名不能为空", 0);
        }
        if (strlen($username) < 4) {
            throw new ApiException('用户名长度不能小于4位', 0);
        }
        if (empty($password)) {
            throw new ApiException("密码不能为空", 0);
        }
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
            throw new ApiException('密码长度不能小于8位，且必须包含一个字母和数字', 0);
        }
        return UserLogic::getInstance()->register($username, $password);
    }

    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        if (empty($username)) {
            throw new ApiException("用户名不能为空", 0);
        }
        if (strlen($username) < 4) {
            throw new ApiException('用户名长度不能小于4位', 0);
        }
        if (empty($password)) {
            throw new ApiException("密码不能为空", 0);
        }
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
            throw new ApiException('密码长度不能小于8位，且必须包含一个字母和数字', 0);
        }
        return UserLogic::getInstance()->login($request->username, $request->password);
    }

    public function logout()
    {
        return UserLogic::getInstance()->logout();
    }
}
