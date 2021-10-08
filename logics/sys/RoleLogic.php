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
use app\models\Role;
use app\models\RolePermission;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class RoleLogic extends BaseLogic
{
    public function map()
    {
        $roles = Role::find()->all();
        return ArrayHelper::map($roles, 'id', 'role_name');
    }

    public function findForPage($search, Page $page)
    {
        $pageResult = new PageResult();
        $pageResult->list = Role::findForPage($search, $page);
        $pageResult->total = Role::findCount($search);
        return $pageResult;
    }

    /**
     * @throws AppException
     */
    public function add($data)
    {
        $role = new Role();
        $role->attributes = $data;
        if (!$role->validate()) {
            throw new AppException(ResultCode::ERROR, VelUtils::getModelError($role));
        }
        $role->save();
        $this->savePermissions($role->id, $data['permIds']);
    }

    /**
     * @param $data
     * @throws AppException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function update($data)
    {
        $role = Role::findOne(['id' => $data['id']]);
        if ($role == null) {
            throw new AppException(ResultCode::ERROR, '无效ID');
        }
        VelUtils::batchCamelToUnderscore($data, ['roleName']);
        $role->attributes = $data;
        if (!$role->validate()) {
            throw new AppException(ResultCode::ERROR, VelUtils::getModelError($role));
        }
        $role->update();
        $this->savePermissions($role->id, $data['permIds']);

        // 和该角色关联的用户需要删除角色和权限缓存
        $users = AdminUser::getAdminUserIdsByRoleId($role->id);
        $this->clearCache(array_column($users, 'id'));
    }

    public function savePermissions($roleId, $permIds)
    {
        RolePermission::deleteAll(['role_id' => $roleId]);
        if (!empty($permIds)) {
            foreach ($permIds as $permId) {
                $rolePerm = new RolePermission();
                $rolePerm->permission_id = $permId;
                $rolePerm->role_id = $roleId;
                $rolePerm->save();
            }
        }
    }

    private function clearCache($userIds)
    {
        foreach ($userIds as $id) {
            RedisUtils::hdel(RedisKeys::USER_ROLES, $id);
            RedisUtils::hdel(RedisKeys::USER_PERMISSIONS, $id);
        }
    }
}