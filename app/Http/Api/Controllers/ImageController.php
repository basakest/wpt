<?php

namespace App\Http\Api\Controllers;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Logic\Image\ImageLogic;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function storeImage() {
        return ImageLogic::getInstance()->storeImage();
    }

    public function getImagesUnderId(Request $request)
    {
        $article_id = $request->input('article_id');
        if (empty($article_id)) {
            throw new ApiException("文章的ID不能为空", 1);
        }
        return ImageLogic::getInstance()->getImagesUnderId($article_id);
    }
}
