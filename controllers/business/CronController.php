<?php

namespace app\controllers\business;

use app\components\libs\ResultCode;
use app\components\libs\ResultData;
use app\controllers\BaseController;

class CronController extends BaseController
{
    public function actionHelloShell()
    {
        $cronSecret = \Yii::$app->params['cron-secret'];
        if ($cronSecret != $this->request('secret')) {
            return ResultData::fail(ResultCode::ERROR, 'secret错误');
        }
        \Yii::info('【hello-shell】 执行。。。');
        return ResultData::ok();
    }
}