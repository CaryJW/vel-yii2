<?php

namespace app\components\upload;

use app\components\upload\impl\AliOssUtil;
use app\components\upload\impl\LocalUploadUtil;
use Exception;

/**
 * 上传文件工厂类
 *
 * @author Cary
 * @date 2021/9/30
 */
class UploadFactory
{
    private static $instance;

    private static $uploadImpl = [
        'local' => LocalUploadUtil::class,
        'ali' => AliOssUtil::class
    ];

    /**
     * @return UploadUtil
     * @throws Exception
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            $uploadType = \Yii::$app->params['uploadType'];
            if (!isset(self::$uploadImpl[$uploadType])) {
                throw new Exception("未找到该上传方式实例");
            }
            $className = self::$uploadImpl[$uploadType];
            self::$instance = new $className();
        }
        return self::$instance;
    }
}