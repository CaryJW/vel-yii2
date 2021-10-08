<?php

namespace app\controllers\sys;

use app\components\libs\AppException;
use app\components\libs\ResultData;
use app\controllers\BaseController;
use app\logics\sys\ConfigureLogic;
use yii\db\StaleObjectException;

/**
 * 配置控制器
 *
 * @author Cary
 * @date 2021/9/28
 * @property ConfigureLogic $logic
 */
class ConfigureController extends BaseController
{
    public function init()
    {
        parent::init();
        $this->logic = new ConfigureLogic();
    }

    /**
     * @throws AppException
     */
    public function actionGetByValue()
    {
        $value = $this->getParams('value');
        return ResultData::ok()->put('configure', $this->logic->getConfigureByValue($value));
    }

    /**
     * @throws AppException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionSavePasswordStrategy()
    {
        $this->logic->savePasswordStrategy($this->postParams());
        return ResultData::ok();
    }
}