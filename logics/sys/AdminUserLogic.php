<?php

namespace app\logics\sys;

use app\components\libs\AppException;
use app\components\libs\Page;
use app\components\libs\PageResult;
use app\components\libs\RedisKeys;
use app\components\libs\ResultCode;
use app\components\utils\RedisUtils;
use app\components\utils\VelUtils;
use app\logics\BaseLogic;
use app\models\AdminUser;
use app\models\AdminUserRole;
use yii\db\StaleObjectException;

class AdminUserLogic extends BaseLogic
{

    public function findForPage($search, Page $page)
    {
        $pageResult = new PageResult();
        $pageResult->list = AdminUser::findForPage($search, $page);
        $pageResult->total = AdminUser::findCount($search);
        return $pageResult;
    }

    /**
     * @throws AppException
     */
    public function add($data)
    {
        $user = new AdminUser();
        $user->attributes = $data;
        if (!$user->validate()) {
            throw new AppException(ResultCode::ERROR, VelUtils::getModelError($user));
        }
        if (AdminUser::find()->where(['username' => $user->username])->exists()) {
            throw new AppException(ResultCode::ERROR, '用户名重复');
        }
        $user->password = md5($user->password);
        $user->save();
        $this->saveRole($user->id, $data['roleIds']);
    }

    /**
     * @param $data
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function update($data)
    {
        $user = AdminUser::findOne(['id' => $data['id']]);
        if ($user == null) {
            throw new AppException(ResultCode::ERROR, '无效ID');
        }
        if (AdminUser::find()->where([
            'and',
            ['username' => $data['username']],
            ['!=', 'id', $user->id]
        ])->exists()) {
            throw new AppException(ResultCode::ERROR, '用户名重复');
        }
        unset($data['password']);
        $user->attributes = $data;
        if (!$user->validate()) {
            throw new AppException(ResultCode::ERROR, VelUtils::getModelError($user));
        }
        $user->update();
        $this->saveRole($data['id'], $data['roleIds']);
        $this->clearCache($user->id);
    }

    private function saveRole($userId, $roleIds)
    {
        AdminUserRole::deleteAll(['admin_user_id' => $userId]);
        if (!empty($roleIds)) {
            foreach ($roleIds as $roleId) {
                $userRole = new AdminUserRole();
                $userRole->admin_user_id = $userId;
                $userRole->role_id = $roleId;
                $userRole->save();
            }
        }
    }

    /**
     * 更新密码
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function updatePassword($id, $oldPassword, $password)
    {
        $user = AdminUser::getById($id);
        if ($user == null) {
            throw new AppException(ResultCode::ERROR, '无效ID');
        }
        if (md5($oldPassword) != $user->password) {
            throw new AppException(ResultCode::ERROR, '原密码错误');
        }
        $user->password = md5($password);
        $user->update();

        $this->clearCache($user->id);
    }

    private function clearCache($userId)
    {
        RedisUtils::hdel(RedisKeys::USER_ROLES, $userId);
        RedisUtils::hdel(RedisKeys::USER_PERMISSIONS, $userId);
    }

    /**
     * 修改用户名
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function updateUsername($id, $username)
    {
        $user = AdminUser::getById($id);
        if ($user == null) {
            throw new AppException(ResultCode::ERROR, '无效ID');
        }
        $user->username = $username;
        $user->update();
    }

    /**
     * 修改头像
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function updateAvatar($id, $url)
    {
        $user = AdminUser::getById($id);
        if ($user == null) {
            throw new AppException(ResultCode::ERROR, '无效ID');
        }
        $user->avatar = $url;
        $user->update();
    }
}