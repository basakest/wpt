<?php

namespace App\Logic\Image;

use App\Exceptions\ApiException;
use App\Logic\Article\ArticleLogic;
use App\Models\ImageModel;
use App\Utils\Singleton;

class ImageLogic
{
    use Singleton;

    public function storeImage()
    {
        reset ($_FILES);
        $temp = current($_FILES);
        // 设置图片保存的文件夹
        $article_num = ArticleLogic::getInstance()->getArticleNum();
        $article_id = $article_num + 1;
        $imageFolder = "images/{$article_id}/";

        if (!file_exists($imageFolder))
        {
            mkdir("$imageFolder", 0777, true);
        }
        $image_num = ImageModel::getInstance()->count(['articleId' => $article_id]);
        $image_id = $image_num + 1;
        $ext = pathinfo($temp['name'], PATHINFO_EXTENSION);
        $file_name = $image_id . '.' . $ext;
        $filetowrite = $imageFolder . $file_name;

        // 都没问题，就将上传数据移动到目标文件夹，此处直接使用原文件名，建议重命名
        if (move_uploaded_file($temp['tmp_name'], $filetowrite)) {
            ImageModel::getInstance()->insertData(['articleId' => $article_id, 'url' => $filetowrite]);
            return $filetowrite;
        } else {
            return $temp['error'];
        }
    }

    public function getImagesUnderId($article_id)
    {
        return ImageModel::getInstance()->getList('url', ['articleId' => $article_id]);
    }
}