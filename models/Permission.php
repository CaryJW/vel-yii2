<?php

namespace app\models;

use app\components\libs\Constants;

/**
 * This is the model class for table "v_permission".
 *
 * @property int $id
 * @property int $pid 父ID
 * @property string $icon 图标
 * @property string $name 页面名称
 * @property string $title 菜单名称
 * @property string $path url路径
 * @property string $component 组件
 * @property string $perms 权限标识
 * @property int $type 类型
 * @property int $sort 排序
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class Permission extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_permission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid', 'type', 'sort'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['icon'], 'string', 'max' => 32],
            [['name', 'title', 'path', 'component', 'perms'], 'string', 'max' => 125],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'icon' => 'Icon',
            'name' => 'Name',
            'title' => 'Title',
            'path' => 'Path',
            'component' => 'Component',
            'perms' => 'Perms',
            'type' => 'Type',
            'sort' => 'Sort',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * 添加自定义属性
     * @return array
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['children']);
    }

    public static function getUserPermissionsByUserId($userId)
    {
        return self::find()
            ->alias('p')
            ->leftJoin(RolePermission::tableName() . ' rp', 'p.id = rp.permission_id')
            ->leftJoin(AdminUserRole::tableName() . ' ur', 'rp.role_id = ur.role_id')
            ->where([
                'and',
                ['ur.admin_user_id' => $userId],
                ['!=', 'p.type', Constants::MENU_LABEL]
            ])
            ->orderBy('p.sort asc')
            ->distinct()
            ->all();
    }

    public static function getUserPermissionsByRoleId($roleId)
    {
        return self::find()
            ->alias('p')
            ->leftJoin(RolePermission::tableName() . ' rp', 'p.id = rp.permission_id')
            ->where(['rp.role_id' => $roleId])
            ->distinct()
            ->all();
    }
}
