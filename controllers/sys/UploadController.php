<?php

namespace app\controllers\sys;

use app\components\libs\ResultData;
use app\components\upload\UploadFactory;
use app\controllers\BaseController;
use yii\web\UploadedFile;

class UploadController extends BaseController
{
    public function actionUpload()
    {
        $image = UploadedFile::getInstanceByName('file');
        if (empty($image)) {
            return ResultData::failMessage('读取文件错误');
        }
        $allowImageSize = \Yii::$app->params['allowImageSize'];
        if ($image->size > $allowImageSize) {
            $allowImageSizeMB = $allowImageSize / 1024;
            return ResultData::failMessage("上传文件允许最大大小{$allowImageSizeMB}MB");
        }
        try {
            $uploadResult = UploadFactory::getInstance()->upload($image);
        } catch (\Exception $e) {
            return ResultData::failMessage('上传失败');
        }
        return ResultData::ok()->put('uploadResult', $uploadResult);
    }
}