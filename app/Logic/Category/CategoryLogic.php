<?php

namespace App\Logic\Category;

use App\Exceptions\ApiException;
use App\Logic\Article\ArticleLogic;
use App\Models\ArticleModel;
use App\Models\CatrgoryModel;
use App\Utils\Singleton;

class CategoryLogic
{
    use Singleton;

    public function getall()
    {
        return CatrgoryModel::getInstance()->getList(['name', 'id'], null);
    }

    public function nameToId($name)
    {
        $category = CatrgoryModel::getInstance()->getOne(['id'], ['name' => $name]);
        if (!$category) {
            throw new ApiException('类别ID不存在', 1);
        }
        return $category->id;
    }

    public function idToName($id)
    {
        $category = CatrgoryModel::getInstance()->getOne(['name'], ['id' => $id]);
        if (!$category) {
            throw new ApiException('类别ID不存在', 1);
        }
        return $category->name;
    }

    public function getAllWithArticles()
    {

        // 一个对象数组，每个对象有一个name属性
        $categories = $this->getall();
        // 现在有了分类名，接下来要为每个分类获取3篇最新的文章
        foreach ($categories as $category) {
            $category->articles = ArticleModel::getInstance()->getList(['id', 'title'], ['categoryId' => $category->id], ['createTime' => 'desc'], null, 3);
            foreach ($category->articles as $article) {
                $article->image = ArticleLogic::getInstance()->getFirstImage($article->id);
                unset($article->id);
            }
        }
        return $categories;

        // 文章还要图片....
        /*
        $categories = $this->getall();
        $res = [];
        $i = 0;
        foreach ($categories as $category) {

            $name = $category->name;
            // $res[$i]是包含三个元素的数组
            $res[$i] = ArticleModel::getInstance()->getJoinList(['category', 'category.id', 'categoryId', 'left'], ['title', 'category.name, article.id'], ['category.name' => $name], ['createTime' => 'desc'], null, 3);
            foreach($res[$i] as $article) {
                $image = ArticleLogic::getInstance()->getFirstImage($article->id);
                $article->image = $image;
            }
            $i++;
            //$res['name'] = ArticleModel::getInstance()->getList(['title']);
        }
        return $res;
        */
    }
}