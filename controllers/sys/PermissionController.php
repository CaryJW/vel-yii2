<?php

namespace app\controllers\sys;

use app\components\libs\AppException;
use app\components\libs\ResultData;
use app\controllers\BaseController;
use app\filters\LogFilter;
use app\logics\sys\PermissionLogic;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

/**
 * 权限控制器
 *
 * @author Cary
 * @date 2021/9/10
 * @property PermissionLogic $logic
 */
class PermissionController extends BaseController
{
    public function init()
    {
        parent::init();
        $this->logic = new PermissionLogic();
    }

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
                'add' => '添加权限',
                'update' => '更新权限',
                'delete' => '删除权限',
            ]
        ];
        return $behaviors;
    }

    public function actionCurrentUser()
    {
        return ResultData::ok()->put('tree', $this->logic->tree());
    }

    public function actionTree()
    {
        return ResultData::ok()->put('tree', $this->logic->completeTree());
    }

    /**
     * @throws AppException
     */
    public function actionAdd()
    {
        return ResultData::ok()->put('permission', $this->logic->add($this->postParams()));
    }

    /**
     * @throws AppException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionUpdate()
    {
        return ResultData::ok()->put('permission', $this->logic->update($this->postParams()));
    }

    /**
     * @return ResultData
     * @throws AppException
     * @throws Throwable
     * @throws Exception
     */
    public function actionDelete()
    {
        $id = $this->getParams('id');
        $this->logic->delete($id);
        return ResultData::ok();
    }

    /**
     * @throws AppException
     */
    public function actionRole()
    {
        $id = $this->getParams('id');
        return ResultData::ok()->put('permIds', $this->logic->getUserPermIdsByRoleId($id));
    }
}