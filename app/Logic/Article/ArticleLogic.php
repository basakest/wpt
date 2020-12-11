<?php

namespace App\Logic\Article;

use App\Exceptions\ApiException;
use App\Logic\Category\CategoryLogic;
use App\Logic\Image\ImageLogic;
use App\Models\ArticleModel;
use App\Models\ImageModel;
use App\Utils\Singleton;
use App\Logic\User\UserLogic;

class ArticleLogic
{
    use Singleton;

    public function _checkArticle($id)
    {
        if (!$this->findById($id)) {
            throw new ApiException('文章不存在', 1);
        }
        if ($this->alreadyDelete($id)) {
            throw new ApiException('该文章已被删除', 1);
        }
    }

    public function createArticle($title, $content, $category_id)
    {
        $user_id = UserLogic::getInstance()->getLoginUserId();
        if ($user_id) {
            return ArticleModel::getInstance()->insertData(['title' => $title, 'content' => $content, 'userId' => $user_id, 'categoryId' => $category_id, 'createTime' => time(), 'updateTime' => time()]);
        } else {
            throw new ApiException('用户未登录，无法创建文章', 0);
        }
    }

    public function getArticleNum()
    {
        return ArticleModel::getInstance()->count(null);
    }

    public function  searchTitle($keyword, $page)
    {
        $limit = 10;
        $num = ArticleModel::getInstance()->count(['title like' => "%$keyword%"]);
        $max_page = ceil($num / $limit);
        $page = $page > $max_page ? $max_page : $page;
        $offset = ($page - 1) * $limit;
        $articles = ArticleModel::getInstance()->getList(['title', 'id', 'userId'], ['title like' => "%$keyword%", 'article.id <' => $num - $offset], ['createTime' => 'desc']);
        foreach($articles as $article) {
            $article->username = UserLogic::getInstance()->idToName($article->userId);
            unset($article->userId);
        }
        $articles['max_page'] = $max_page;
        return $articles;
        //return ArticleModel::getInstance()->getJoinList(['user', 'user.id', 'article.userId', 'left'], ['article.id', 'title', 'user.username as author'],['article.id <' => $num - $offset], ['createTime' => 'desc', 'id' => 'asc'], null, 10);
        //return ArticleModel::getInstance()->getJoinList(['user', 'user.id', 'article.userId', 'join'], ['title', 'content', 'user.username', "timediff(now(), FROM_UNIXTIME(createTime)) AS time"], ['title like' => "%$keyword%"]);
        //return ArticleModel::getInstance()->getJoinList(['user', 'user.id', 'article.userId', 'join'], ['title', 'content', 'user.username', "now() AS date_formatted"], ['title like' => "%$keyword%"] );
    }

    public function searchTitleUnderId($keyword, $category_id, $page)
    {
        $limit = 15;
        $num = ArticleModel::getInstance()->count(['title like' => "%$keyword%"]);
        $max_page = ceil($num / $limit);
        $page = $page > $max_page ? $max_page : $page;
        $offset = ($page - 1) * $limit;
        $articles = ArticleModel::getInstance()->getList(['title', 'id', 'userId'], ['title like' => "%$keyword%", 'article.id <' => $num - $offset, 'categoryId' => $category_id], ['createTime' => 'desc']);
        foreach($articles as $article) {
            $article->username = UserLogic::getInstance()->idToName($article->userId);
            unset($article->userId);
        }
        return $articles;
        //return ArticleModel::getInstance()->getJoinList(['user', 'user.id', 'article.userId', 'join'], ['title', 'content', 'user.username', 'timediff(now(), FROM_UNIXTIME(createTime)) AS time'], ['title like' => "%$keyword%", 'categoryId' => $category_id]);
    }

    public function findById($id)
    {
        if ($this->alreadyDelete($id)) {
            throw new ApiException('该文章已被删除', 1);
        }
        return ArticleModel::getInstance()->getOne(['id'], ['id' => $id]);
    }

    public function alreadyDelete($id)
    {
        return ArticleModel::getInstance()->getOne(['id'], ['id' => $id, 'hasDelete' => 1]);
    }

    public function deleteArticle($id)
    {
        $this->_checkArticle($id);
        return ArticleModel::getInstance()->updateData(['hasDelete' => 1], ['id' => $id]);
    }

    public function getById($id)
    {
        $this->_checkArticle($id);
        $article = ArticleModel::getInstance()->getJoinOne([['user', 'user.id', 'article.userId', 'left'], ['category', 'category.id', 'article.categoryId', 'left']], ['title', 'content', 'user.username as username', 'category.name as category', 'timediff(now(), FROM_UNIXTIME(updateTime)) AS time'], ['article.id' => $id]);
        $image = ImageLogic::getInstance()->getImagesUnderId($id);
        return ['article' => $article, 'image' => $image];
    }

    public function getFirstImage($article_id)
    {
        return ImageModel::getInstance()->getOne('url', ['articleId' => $article_id], ['url' => 'asc']) ? ImageModel::getInstance()->getOne('url', ['articleId' => $article_id], ['url' => 'asc'])->url : '';
    }

    public function getContentByPage($page)
    {
        $limit = 10;
        $num = ArticleModel::getInstance()->count(null);
        $max_page = ceil($num / $limit);
        $page = $page > $max_page ? $max_page : $page;
        $offset = ($page - 1) * $limit;
        $articles = ArticleModel::getInstance()->getList(['id', 'title', 'categoryId', 'createTime'], ['article.id <' => $num - $offset], ['createTime' => 'desc', 'id' => 'asc'], null, 10);
        foreach($articles as $article) {
            $article->time = date('Y-m-d H:i:s', $article->createTime);
            $article->max_age = $max_page;
            $article->num = $num;
            $article->category = CategoryLogic::getInstance()->idToName($article->categoryId);
            unset($article->categoryId);
        }

         /*
        foreach($articles as $article) {
            $article->username = UserLogic::getInstance()->idToName($article->userId);
            unset($article->userId);
        }
        */
        return $articles;
        //return ArticleModel::getInstance()->getJoinList(['user', 'user.id', 'article.userId', 'left'], ['article.id', 'title', 'user.username as author'],['article.id <' => $num - $offset], ['createTime' => 'desc', 'id' => 'asc'], null, 10);
    }

    public function getNewArticle($num)
    {
        return ArticleModel::getInstance()->getList(['title'], null, ['createTime' => 'desc'], null, $num);
    }

    public function getNewArticleUnderCategory($num,  $category_id)
    {
        return ArticleModel::getInstance()->getList(['title'], ['categoryId' => $category_id], ['createTime' => 'desc'], null, $num);

        //return ArticleModel::getInstance()->getList(['title'], ['categoryId' => $category_id], ['createTime' => 'desc'], null, $num);
    }

    public function getNewestArticle()
    {
        return (array) ArticleModel::getInstance()->getOne(['title', 'content'], null, ['createTime' => 'desc']);
    }

    public function updateArticle($id, $title, $content)
    {
        $this->_checkArticle($id);
        return ArticleModel::getInstance()->updateData(['title' => $title, 'content' => $content], ['id' => $id]);
    }

    public function getUnderCategory($id, $page)
    {
        $limit = 10;
        $num = ArticleModel::getInstance()->count(['categoryId' => $id]);
        $max_page = ceil($num / $limit);
        $page = $page > $max_page ? $max_page : $page;
        $offset = ($page - 1) * $limit;

        $articles = ArticleModel::getInstance()->getList(['title', 'id', 'userId'], ['categoryId' => $id], ['createTime' => 'desc'], null, $limit, $offset);
        foreach($articles as $article) {
            $article->username = UserLogic::getInstance()->idToName($article->userId);
        }
        $articles['max_page'] = $max_page;
        return $articles;
    }
}