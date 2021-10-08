<?php

namespace app\components\upload\impl;

use app\components\upload\UploadResult;
use app\components\upload\UploadUtil;
use app\components\utils\VelFileUtils;
use OSS\Core\OssException;
use OSS\OssClient;
use yii\web\UploadedFile;

/**
 * 阿里云oss上传
 *
 * @author Cary
 * @date 2021/9/30
 */
class AliOssUtil implements UploadUtil
{
    private $ossClient;
    private $bucketName;
    private $object;
    private $publicBucketName;

    public function __construct()
    {
        $ali = \Yii::$app->params['upload']['ali'];
        $this->bucketName = $ali['bucketName'];
        $this->object = $ali['filePath'];
        $this->publicBucketName = $ali['publicBucketName'];

        try {
            $this->ossClient = new OssClient($ali['accessKeyId'], $ali['accessKeySecret'], $ali['endpoint']);
        } catch (OssException $e) {
            \Yii::error("文件上传失败：{$e->getMessage()}");
        }
    }

    function type()
    {
        return 'ali';
    }


    /**
     * @throws OssException
     */
    function upload(UploadedFile $uploadedFile)
    {
        $fileName = uniqid() . '.' . $uploadedFile->getExtension();
        $file = VelFileUtils::getTempFile($fileName);
        $uploadedFile->saveAs($file);
        return $this->fileNameUpload($file, $fileName);
    }

    /**
     * @throws OssException
     */
    function fileNameUpload($file, $fileName)
    {
        try {
            $this->ossClient->uploadFile($this->bucketName, $this->object . $fileName, $file);
        } catch (OssException $e) {
            \Yii::error("文件上传失败：{$e->getMessage()}");
            throw $e;
        } finally {
            unlink($file);
        }
        $result = new UploadResult();
        $result->fileName = $fileName;
        $result->url = $this->publicBucketName . '/' . $this->object . $fileName;
        return $result;
    }

    function uploadBase64($base64)
    {
        // TODO: Implement uploadBase64() method.
    }

    function delete($url)
    {
        $filePath = str_replace($this->publicBucketName . '/', '', $url);
        $result = $this->ossClient->deleteObject($this->bucketName, $filePath);
        if (empty($result)) {
            return false;
        }
        return true;
    }
}