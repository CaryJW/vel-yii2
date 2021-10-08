<?php

namespace app\logics\sys;

use app\components\authentication\UserHelper;
use app\components\libs\AppException;
use app\components\libs\Constants;
use app\components\libs\RedisKeys;
use app\components\libs\ResultCode;
use app\components\utils\RedisUtils;
use app\components\utils\VelUtils;
use app\logics\BaseLogic;
use app\models\Permission;
use app\models\RolePermission;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class PermissionLogic extends BaseLogic
{
    public function tree()
    {
        $permissions = Permission::getUserPermissionsByUserId(UserHelper::getId());
        return $this->paresTree(null, $permissions);
    }

    public function paresTree($perm, $perms)
    {
        $nodes = [];
        $pid = $perm == null ? 0 : $perm->id;
        foreach ($perms as $p) {
            if ($p->pid == $pid) {
                $p->children = $this->paresTree($p, $perms);
                $nodes[] = $p;
            }
        }
        return $nodes;
    }

    public function completeTree()
    {
        if (RedisUtils::exists(RedisKeys::MENU)) {
            return RedisUtils::get(RedisKeys::MENU, true);
        }
        $permissions = Permission::find()->all();
        $tree = $this->paresTree(null, $permissions);

        RedisUtils::set(RedisKeys::MENU, $tree);
        return $tree;
    }

    /**
     * @param $data
     * @return Permission
     * @throws AppException
     */
    public function add($data)
    {
        $permission = new Permission();
        $this->saveOrUpdateData($data, $permission);
        $permission->save();
        RedisUtils::del(RedisKeys::MENU);
        return $permission;
    }

    /**
     * @param $data
     * @return Permission
     * @throws AppException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function update($data)
    {
        $permission = Permission::findOne(['id' => $data['id']]);
        if ($permission == null) {
            throw new AppException(ResultCode::ERROR, '无效ID');
        }
        $this->saveOrUpdateData($data, $permission);
        $permission->update();
        RedisUtils::del(RedisKeys::MENU);
        return $permission;
    }

    /**
     * @throws AppException
     * @throws Exception
     * @throws Throwable
     */
    public function delete($id)
    {
        $permission = Permission::findOne(['id' => $id]);
        if ($permission == null) {
            throw new AppException(ResultCode::ERROR, '无效ID');
        }
        if (Permission::find()->where(['pid' => $permission->id])->exists()) {
            throw new AppException(ResultCode::ERROR, '存在子菜单不能删除');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $permission->delete();
            RolePermission::deleteAll((['permission_id' => $permission->id]));
            $transaction->commit();
            RedisUtils::del(RedisKeys::MENU);
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function getUserPermIdsByRoleId($roleId)
    {
        return ArrayHelper::getColumn(Permission::getUserPermissionsByRoleId($roleId), 'id');
    }

    /**
     * @throws AppException
     */
    private function saveOrUpdateData($data, Permission $permission)
    {
        $permission->attributes = $data;
        if (!$permission->validate()) {
            throw new AppException(ResultCode::ERROR, VelUtils::getModelError($permission));
        }
        if ($permission->type != Constants::MENU_LABEL) {
            $p = Permission::findOne(['perms' => $permission->perms]);
            if ($p != null && ($permission->id == 0 || $p->id != $permission->id)) {
                throw new AppException(ResultCode::ERROR, '权限标识重复');
            }
        }

        if ($permission->type == Constants::MENU_BUTTON) {
            $permission->title = '';
            $permission->icon = '';
            $permission->path = '';
            $permission->component = '';
        }
    }
}