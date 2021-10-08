<?php

namespace app\models;

use app\components\libs\Page;

/**
 * This is the model class for table "v_role".
 *
 * @property int $id
 * @property string $role_name 角色名称
 * @property string $remarks 角色描述
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class Role extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['create_time', 'update_time'], 'safe'],
            [['role_name'], 'string', 'max' => 125],
            [['remarks'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_name' => 'Role Name',
            'remarks' => 'Remarks',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    public static function getUserRoleByUserId($userId)
    {
        return self::find()
            ->alias('r')
            ->leftJoin(AdminUserRole::tableName() . ' ur', 'r.id = ur.role_id')
            ->where(['ur.admin_user_id' => $userId])
            ->distinct()
            ->all();
    }

    private static function parseWhere($search)
    {
        $where = ['and'];
        if (!empty($search['roleName'])) {
            $where[] = ['like', 'role_name', $search['roleName']];
        }
        return $where;
    }

    public static function findForPage($search, Page $page)
    {
        $where = self::parseWhere($search);
        return self::find()
            ->where($where)
            ->offset($page->offset)
            ->limit($page->limit)
            ->orderBy($page->sort)
            ->all();
    }

    public static function findCount($search)
    {
        $where = self::parseWhere($search);
        return self::find()
            ->where($where)
            ->count(1);
    }
}
