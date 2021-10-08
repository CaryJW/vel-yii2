<?php

namespace app\components\utils;

use app\components\libs\Constants;
use Exception;

class VelFileUtils
{
    /**
     * 下载文件
     * @param $file
     * @param null $fileName
     * @param bool $delete
     * @throws Exception
     */
    public static function download($file, $fileName = null, $delete = true)
    {
        if (!file_exists($file)) {
            throw new Exception('文件不存在');
        }

        $fileType = self::getFileType($file);
        if (!self::fileTypeIsValid($fileType)) {
            throw new Exception('暂不支持该类型文件下载');
        }
        \Yii::$app->response->sendFile($file, $fileName)->send();
        if ($delete) {
            unlink($file);
        }
    }

    /**
     * 获取文件扩展名
     * @param $filename
     * @return array|false|string|string[]
     */
    private static function getFileType($filename)
    {
        $ext = substr($filename, strrpos($filename, '.'));
        return str_replace('.', '', $ext);
    }

    /**
     * 判断文件是否允许下载
     * @param $type
     * @return bool
     */
    private static function fileTypeIsValid($type)
    {
        return in_array($type, Constants::VALID_FILE_TYPE);
    }

    /**
     * 创建临时文件
     * @param $fileName
     * @return string
     */
    public static function getTempFile($fileName)
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;
    }
}