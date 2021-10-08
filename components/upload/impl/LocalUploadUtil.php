<?php

namespace app\components\upload\impl;

use app\components\upload\UploadResult;
use app\components\upload\UploadUtil;
use app\components\utils\VelFileUtils;
use Exception;
use yii\web\UploadedFile;

class LocalUploadUtil implements UploadUtil
{
    private $filePath;

    public function __construct()
    {
        $local = \Yii::$app->params['upload']['local'];
        $this->filePath = $local['filePath'];
    }

    function type()
    {
        return 'local';
    }

    function upload(UploadedFile $uploadedFile)
    {
        $fileName = uniqid() . '.' . $uploadedFile->getExtension();
        $file = VelFileUtils::getTempFile($fileName);
        $uploadedFile->saveAs($file);
        return $this->fileNameUpload($file, $fileName);
    }

    /**
     * @throws Exception
     */
    function fileNameUpload($file, $fileName)
    {
        $uploadPath = $this->filePath . '/' . CURRENT_DATE;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        $newFile = $uploadPath . '/' . $fileName;
        try {
            copy($file, $newFile);
        } catch (Exception $e) {
            \Yii::error("文件上传失败：{$e->getMessage()}");
            throw $e;
        }
        $result = new UploadResult();
        $result->fileName = $fileName;
        $result->url = \Yii::$app->request->hostInfo . '/' . $newFile;
        return $result;
    }

    function uploadBase64($base64)
    {
        // TODO: Implement uploadBase64() method.
    }

    function delete($url)
    {
        $filePath = str_replace(\Yii::$app->request->hostInfo . '/', '', $url);
        $result = unlink($filePath);
        if (empty($result)) {
            return false;
        }
        return true;
    }
}