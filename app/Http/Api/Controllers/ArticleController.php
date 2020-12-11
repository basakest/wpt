<?php

namespace App\Http\Api\Controllers;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Logic\Article\ArticleLogic;
use App\Logic\Article\CategoryLogic;
use Illuminate\Http\Request;
use App\Logic\Article\ImageLogic;

class ArticleController extends Controller
{
    public function create(Request $request)
    {
        $title = $request->input('title');
        $content = $request->input('content');
        $category_id = $request->input('category_id');
        if (empty($title)) {
            throw new ApiException("文章标题不能为空", 1);
        }
        if (strlen($title) < 5) {
            throw new ApiException('文章标题不能小于5位', 1);
        }
        if (empty($content)) {
            throw new ApiException("内容不能为空", 1);
        }
        if (strlen($content) < 10) {
            throw new ApiException('文章内容不能小于10位', 0);
        }
        return ArticleLogic::getInstance()->createArticle($title, $content, $category_id);
    }

    public function searchTitle(Request $request)
    {
        $keyword = $request->input('keyword');
        $page = $request->input('page') ?? 1;
        if (empty($keyword)) {
            throw new ApiException("搜索的关键字不能为空", 1);
        }
        return ArticleLogic::getInstance()->searchTitle($keyword, $page);
    }

    public function searchTitleUnderId(Request $request)
    {
        $keyword = $request->input('keyword');
        $category_id = $request->input('category_id');
        $page = $request->input('page') ?? 1;
        if (!$keyword) {
            throw new ApiException("搜索的关键字不能为空", 1);
        }
        if (!$category_id) {
            throw new ApiException("类别ID不能为空", 1);
        }
        return ArticleLogic::getInstance()->searchTitleUnderId($keyword, $category_id, $page);
    }

    public function deleteArticle(Request $request)
    {
        $article_id = $request->input('article_id');
        if (empty($article_id)) {
            throw new ApiException("文章的ID不能为空", 1);
        }
        return ArticleLogic::getInstance()->deleteArticle($article_id);
    }

    public function getById(Request $request)
    {
        $article_id = $request->input('article_id');
        if (empty($article_id)) {
            throw new ApiException("文章的ID不能为空", 1);
        }
        return ArticleLogic::getInstance()->getById($article_id);
    }

    public function getFirstImage(Request $request)
    {
        $article_id = $request->input('article_id');
        if (!$article_id) {
            throw new ApiException("文章的ID不能为空", 1);
        }
        return ArticleLogic::getInstance()->getFirstImage($article_id);
    }

    public function getContentByPage(Request $request)
    {
        $page = $request->input('page') ?? 1;
        return ArticleLogic::getInstance()->getContentByPage($page);
    }

    public function getNewArticle(Request $request)
    {
        $num = $request->input('num') ?? 7;
        return ArticleLogic::getInstance()->getNewArticle($num);
    }

    public function getNewArticleUnderCategory(Request $request)
    {
        $num = $request->input('num') ?? 3;
        $category_id = $request->input('category_id');
        if (!$category_id) {
            throw new ApiException('类别ID不能为空', 1);
        }
        return ArticleLogic::getInstance()->getNewArticleUnderCategory($num, $category_id);
    }

    public function getNewestArticle()
    {
        return ArticleLogic::getInstance()->getNewestArticle();
    }

    public function updateArticle(Request $request)
    {
        $id = $request->input('id');
        $title = $request->input('title');
        $content = $request->input('content');
        if (empty($id)) {
            throw new ApiException('文章ID不能为空', 1);
        }
        if (empty($title)) {
            throw new ApiException('标题不能为空', 1);
        }
        if (empty($content)) {
            throw new ApiException('内容不能为空', 1);
        }
        return ArticleLogic::getInstance()->updateArticle($id, $title, $content);
    }

    public function getUnderCategory(Request $request)
    {
        $category_id = $request->input('category_id');
        $page = $request->input('page') ?? 1;
        if (!$category_id) {
            throw new ApiException('分类ID不能为空', 1);
        }
        return ArticleLogic::getInstance()->getUnderCategory($category_id, $page);
    }
}
