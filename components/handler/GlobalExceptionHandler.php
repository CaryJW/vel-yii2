<?php

namespace app\components\handler;

use app\components\libs\AppException;
use app\components\libs\ResultCode;
use app\components\libs\ResultData;
use yii\web\ErrorHandler;
use yii\web\HttpException;
use yii\web\Response;

/**
 * 全局异常处理
 *
 * @author Cary
 * @date 2021/9/7
 */
class GlobalExceptionHandler extends ErrorHandler
{
    protected function renderException($exception)
    {
        $response = \Yii::$app->getResponse();
        // 自定义异常
        if ($exception instanceof AppException) {
            $response->setStatusCode(200);
            $resultData = ResultData::fail($exception->getCode(), $exception->getMessage());
            if ($exception->data) {
                $resultData->data = $exception->data;
            }
            $response->data = $resultData;
        } else {
            // 调试模式显示异常信息
            if (YII_DEBUG) {
                $response->format = Response::FORMAT_HTML;
                parent::renderException($exception);
            } else {
                $this->handlerException($exception, $response);
            };
        }
        $response->send();
    }

    private function handlerException($exception, $response)
    {
        // 路由异常
        if ($exception instanceof HttpException) {
            $response->setStatusCodeByException($exception);
            $response->data = ResultData::fail(ResultCode::HTTP_ERROR, $exception->getMessage());
        } else {
            \Yii::error($exception);
            $response->setStatusCode(500);
            $response->data = ResultData::fail(ResultCode::SERVER_ERROR, '系统内部异常');
        }
    }
}