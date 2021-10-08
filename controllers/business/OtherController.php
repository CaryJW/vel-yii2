<?php

namespace app\controllers\business;

use app\components\libs\AppException;
use app\components\libs\ResultData;
use app\controllers\BaseController;
use app\logics\business\OtherLogic;
use app\models\Tinymce;
use yii\db\StaleObjectException;

/**
 * OtherController
 *
 * @author Cary
 * @date 2021/9/26
 * @property OtherLogic $logic
 */
class OtherController extends BaseController
{
    public function init()
    {
        parent::init();
        $this->logic = new OtherLogic();
    }

    public function actionGetTinymce()
    {
        return ResultData::ok()->put('content', Tinymce::findOne(['id' => 1])->content);
    }

    /**
     * @return ResultData
     * @throws AppException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionSaveTinymce()
    {
        $this->logic->saveTinymce($this->postParams('content'));
        return ResultData::ok();
    }
}