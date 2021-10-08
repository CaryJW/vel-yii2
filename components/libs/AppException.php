<?php

namespace app\components\libs;

use yii\base\UserException;

/**
 * 自定义异常
 *
 * @author Cary
 * @date 2021/9/7
 */
class AppException extends UserException
{
    public $data;

    public function __construct($code, $message = '', $data = null, $previous = null)
    {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }
}