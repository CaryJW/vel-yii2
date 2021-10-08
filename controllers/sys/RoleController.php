<?php

namespace app\controllers\sys;

use app\components\libs\AppException;
use app\components\libs\ResultData;
use app\controllers\BaseController;
use app\filters\LogFilter;
use app\logics\sys\RoleLogic;
use yii\db\StaleObjectException;

/**
 * 角色控制器
 *
 * @author Cary
 * @date 2021/9/13
 * @property RoleLogic $logic
 */
class RoleController extends BaseController
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
                'add' => '添加角色',
                'update' => '修改角色',
            ]
        ];
        return $behaviors;
    }

    public function init()
    {
        parent::init();
        $this->logic = new RoleLogic();
    }

    public function actionMap()
    {
        return ResultData::ok()->put('roleMap', $this->logic->map());
    }

    public function actionList()
    {
        $search = $this->request();
        $page = $this->buildPage();
        return ResultData::ok()->putPage($this->logic->findForPage($search, $page));
    }

    /**
     * @throws AppException
     */
    public function actionAdd()
    {
        $this->logic->add($this->postParams());
        return ResultData::ok();
    }

    /**
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionUpdate()
    {
        $this->logic->update($this->postParams());
        return ResultData::ok();
    }
}