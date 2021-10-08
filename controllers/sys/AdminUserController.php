<?php

namespace app\controllers\sys;

use app\components\libs\AppException;
use app\components\libs\ResultData;
use app\controllers\BaseController;
use app\filters\LogFilter;
use app\logics\sys\AdminUserLogic;
use yii\db\StaleObjectException;

/**
 * AdminUser控制器
 *
 * @author Cary
 * @date 2021/9/6
 * @property AdminUserLogic $logic
 */
class AdminUserController extends BaseController
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
                'add' => '添加管理员用户',
                'update' => '修改管理员用户',
                'updatePassword' => '修改管理员用户密码',
            ]
        ];
        return $behaviors;
    }

    public function init()
    {
        parent::init();
        $this->logic = new AdminUserLogic();
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
     * @throws StaleObjectException
     * @throws AppException
     * @throws \Throwable
     */
    public function actionUpdate()
    {
        $this->logic->update($this->postParams());
        return ResultData::ok();
    }

    /**
     * 修改密码
     * @return ResultData
     * @throws \Throwable
     * @throws AppException
     * @throws StaleObjectException
     */
    public function actionUpdatePassword()
    {
        list($id, $oldPassword, $password) = $this->postParams('id', 'oldPassword', 'password');
        $this->logic->updatePassword($id, $oldPassword, $password);
        return ResultData::ok();
    }

    /**
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionOwnUpdatePassword()
    {
        list($id, $oldPassword, $password) = $this->postParams('id', 'oldPassword', 'password');
        $this->logic->updatePassword($id, $oldPassword, $password);
        return ResultData::ok();
    }

    /**
     * 修改用户名
     * @return ResultData
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionUpdateUsername()
    {
        list($id, $username) = $this->postParams('id', 'username');
        $this->logic->updateUsername($id, $username);
        return ResultData::ok();
    }

    /**
     * 修改头像
     * @return ResultData
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionUpdateAvatar()
    {
        list($id, $url) = $this->postParams('id', 'url');
        $this->logic->updateAvatar($id, $url);
        return ResultData::ok();
    }
}