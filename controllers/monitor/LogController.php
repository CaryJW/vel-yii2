<?php

namespace app\controllers\monitor;

use app\components\libs\AppException;
use app\components\libs\ResultData;
use app\controllers\BaseController;
use app\filters\LogFilter;
use app\logics\monitor\LogLogic;
use yii\db\StaleObjectException;

/**
 * 日志控制器
 *
 * @author Cary
 * @date 2021/9/26
 * @property LogLogic $logic
 */
class LogController extends BaseController
{
    /**
     * 绑定行为
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 记录日志
        $behaviors['logFilter'] = [
            'class' => LogFilter::class,
            'optional' => [
                'delete' => '删除系统日志',
            ]
        ];
        return $behaviors;
    }

    public function init()
    {
        parent::init();
        $this->logic = new LogLogic();
    }

    public function actionList()
    {
        $search = $this->request();
        $page = $this->buildPage();
        return ResultData::ok()->putPage($this->logic->findForPage($search, $page));
    }

    /**
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionDelete()
    {
        $id = $this->getParams('id');
        $this->logic->delete($id);
        return ResultData::ok();
    }

    public function actionExport()
    {
        $search = $this->request();
        $page = $this->buildPage();
        $this->logic->export($search, $page);
    }

    public function actionBatchExport()
    {
        return ResultData::failMessage('PHP不支持批量导出');
    }
}