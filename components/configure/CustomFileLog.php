<?php

namespace app\components\configure;

use yii\log\FileTarget;
use yii\log\Logger;
use yii\web\Request;

/**
 * 格式化日志
 *
 * @author Cary
 * @date 2021/9/7
 */
class CustomFileLog extends FileTarget
{

    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        $datetime = date('Y-m-d H:i:s', $timestamp);
        $level = Logger::getLevelName($level);
        $request = \Yii::$app->getRequest();
        $ip = $request instanceof Request ? $request->getUserIP() : '-';
        return "[{$datetime}] [{$ip}] [{$level}] [{$category}] [{$text}] \n";
    }
}