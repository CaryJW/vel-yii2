<?php

namespace app\models;

/**
 * This is the model class for table "v_role_permission".
 *
 * @property int $role_id
 * @property int $permission_id
 */
class RolePermission extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_role_permission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_id', 'permission_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'role_id' => 'Role ID',
            'permission_id' => 'Permission ID',
        ];
    }
}
