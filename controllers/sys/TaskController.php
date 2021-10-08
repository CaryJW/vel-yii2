<?php

namespace app\controllers\sys;

use app\components\libs\ResultData;
use app\controllers\BaseController;

class TaskController extends BaseController
{
    public function actionList()
    {
        return ResultData::ok()->put('list', []);
    }
}