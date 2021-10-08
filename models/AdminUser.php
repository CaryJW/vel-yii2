<?php

namespace app\models;

use app\components\libs\AppException;
use app\components\libs\Constants;
use app\components\libs\Page;
use app\components\libs\RedisKeys;
use app\components\libs\ResultCode;
use app\components\utils\JwtUtils;
use app\components\utils\RedisUtils;
use stdClass;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "v_admin_user".
 *
 * @property int $id
 * @property string $username 用户名
 * @property string $realname 真实姓名
 * @property string $avatar 头像
 * @property int $status 状态
 * @property string $password 密码
 * @property string $login_time 登录时间
 * @property string $unlock_time 解锁时间
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class AdminUser extends BaseModel implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_admin_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['login_time', 'unlock_time', 'create_time', 'update_time'], 'safe'],
            [['username'], 'string', 'max' => 64],
            [['realname', 'password'], 'string', 'max' => 32],
            [['avatar'], 'string', 'max' => 125],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'realname' => 'Realname',
            'avatar' => 'Avatar',
            'status' => 'Status',
            'password' => 'Password',
            'login_time' => 'Login Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * 增加、删除、重命名和重定义字段
     * Model::toArray 可输出字段数组
     * @return array|false|int[]|string[]
     */
    public function fields()
    {
        $fields = parent::fields();
        // 去掉一些包含敏感信息的字段
        unset($fields['password']);
        // 添加字段
        $fields[] = 'roles';
        return $fields;
    }

    /**
     * 额外可用字段，通过toArray()方法指定$expand参数来返回这些额外可用字段
     * Model::toArray([], ['roles']
     * @return string[]
     */
    public function extraFields()
    {
        return ['roles' => 'roles'];
    }

    /**
     * 属性
     * $model->attributes 可输出属性数组
     * @return array
     */
    public function attributes()
    {
        return parent::attributes();
    }

    /**
     * @throws InvalidConfigException
     */
    public function getRoles()
    {
        return $this->hasMany(Role::class, ['id' => 'role_id'])
            ->viaTable(AdminUserRole::tableName(), ['admin_user_id' => 'id']);
    }


    public static function getById($id)
    {
        return self::find()
            ->where(['id' => $id])
            ->one();
    }

    public static function getByUsername($username)
    {
        return self::find()
            ->where(['username' => $username])
            ->one();
    }

    public static function findIdentity($id)
    {
        return self::getById($id);
    }

    /**
     * @throws AppException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $userId = JwtUtils::getUserIdByToken($token);
        //redis防止重复登录
        $rToken = RedisUtils::hget(RedisKeys::USER_LOGIN, $userId);
        if ($rToken == null || $rToken != $token) {
            throw new AppException(ResultCode::AUTHORIZED_ERROR, '登录过期，请重新登录');
        }
        $user = AdminUser::getById($userId);
        if ($user == null) {
            throw new AppException(ResultCode::UNAUTHORIZED, '非法账户');
        }
        if (Constants::ADMIN_USER_STATUS_LOCK == $user->status) {
            throw new AppException(ResultCode::ACCOUNT_LOCKED, '账号已被锁定,请联系管理员');
        }
        return $user;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }

    public function doGetAuthorizationInfo()
    {
        $authorizationInfo = new StdClass();
        $userId = $this->getId();
        if (RedisUtils::hexists(RedisKeys::USER_ROLES, $userId) &&
            RedisUtils::hexists(RedisKeys::USER_PERMISSIONS, $userId)) {
            // 缓存中获取角色和权限
            $authorizationInfo->roles = RedisUtils::hget(RedisKeys::USER_ROLES, $userId, true);
            $authorizationInfo->stringPermissions = RedisUtils::hget(RedisKeys::USER_PERMISSIONS, $userId, true);
        } else {
            // 获取用户角色集
            $roleObjs = Role::getUserRoleByUserId($userId);
            $roles = ArrayHelper::getColumn($roleObjs, 'role_name');
            // 获取用户权限集
            $permissionObjs = Permission::getUserPermissionsByUserId($userId);
            $permissions = ArrayHelper::getColumn($permissionObjs, 'perms');
            // 存入redis
            RedisUtils::hset(RedisKeys::USER_ROLES, $userId, $roles);
            RedisUtils::hset(RedisKeys::USER_PERMISSIONS, $userId, $permissions);

            $authorizationInfo->roles = $roles;
            $authorizationInfo->stringPermissions = $permissions;
        }
        return $authorizationInfo;
    }

    private static function parseWhere($search)
    {
        $where = ['and'];
        if (!is_null($search['status']) && $search['status'] != '') {
            $where[] = ['status' => $search['status']];
        }
        if (!empty($search['username'])) {
            $where[] = ['like', 'username', $search['username']];
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
            ->with('roles')
            ->all();
    }

    public static function findCount($search)
    {
        $where = self::parseWhere($search);
        return self::find()
            ->where($where)
            ->count(1);
    }

    public static function getAdminUserIdsByRoleId($roleId)
    {
        return self::find()
            ->alias('u')
            ->leftJoin(AdminUserRole::tableName() . ' ur', 'u.id = ur.admin_user_id')
            ->where(['ur.role_id' => $roleId])
            ->distinct()
            ->all();
    }
}
