<?php

namespace app\models;

/**
 * This is the model class for table "v_admin_user_role".
 *
 * @property int $admin_user_id
 * @property int $role_id
 */
class AdminUserRole extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_admin_user_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['admin_user_id', 'role_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'admin_user_id' => 'Admin User ID',
            'role_id' => 'Role ID',
        ];
    }
}
