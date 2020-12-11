<?php

namespace App\Http\Api\Controllers;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Logic\Category\CategoryLogic;
use App\Models\UserModel;
use Illuminate\Http\Request;
use App\Logic\User\ArticleLogic;

class CategoryController extends Controller
{
    public function getAll()
    {
        return CategoryLogic::getInstance()->getall();
    }

    public function getArticles(Request $request)
    {
        $category_id = $request->input('category_id');
        if (empty($category_id)) {
            throw new ApiException('类别ID不能为空');
        }
        return CategoryLogic::getInstance()->getArticles($category_id);
    }

    public function getAllWithArticles()
    {
        return CategoryLogic::getInstance()->getAllWithArticles();
    }
}
