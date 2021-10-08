<?php

namespace app\components\configure;

use yii\helpers\BaseInflector;
use yii\helpers\Inflector;

/**
 * 响应配置
 *
 * @author Cary
 * @date 2021/9/29
 */
class Response extends \yii\web\Response
{
    /**
     * 设置导出文件头
     * @param string $disposition
     * @param string $attachmentName
     * @return string
     */
    protected function getDispositionHeaderValue($disposition, $attachmentName)
    {
        $fallbackName = str_replace(
            ['%', '/', '\\', '"', "\x7F"],
            ['_', '_', '_', '\\"', '_'],
            Inflector::transliterate($attachmentName, BaseInflector::TRANSLITERATE_LOOSE)
        );
        $utfName = rawurlencode(str_replace(['%', '/', '\\'], '', $attachmentName));

        $dispositionHeader = "{$disposition}; filename={$fallbackName}";
        if ($utfName !== $fallbackName) {
            $dispositionHeader = "{$disposition}; filename={$utfName}";
        }

        return $dispositionHeader;
    }
}