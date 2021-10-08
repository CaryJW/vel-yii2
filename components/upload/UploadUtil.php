<?php

namespace app\components\upload;

use yii\web\UploadedFile;

/**
 * 文件上传工具
 */
interface UploadUtil
{
    /**
     * 上传类型
     * @return mixed
     */
    function type();

    /**
     * 上传文件
     * @param UploadedFile $uploadedFile
     * @return mixed
     */
    function upload(UploadedFile $uploadedFile);

    /**
     * 上传文件(自定义文件名)
     * @param $file
     * @param $fileName
     * @return mixed
     */
    function fileNameUpload($file, $fileName);

    /**
     * 上传base64格式的文件
     * @param $base64
     * @return mixed
     */
    function uploadBase64($base64);

    /**
     * 删除文件
     * @param $url
     * @return mixed
     */
    function delete($url);
}