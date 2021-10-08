<?php

namespace app\components\authentication;

use app\components\libs\RedisKeys;
use app\components\utils\RedisUtils;
use yii\web\IdentityInterface;

/**
 * 用户帮助类
 *
 * @author Cary
 * @date 2021/9/9
 */
class UserHelper
{
    /**
     * 获取当前用户信息
     * @return IdentityInterface|null
     */
    public static function getCurrentUser()
    {
        return \Yii::$app->user->identity;
    }

    /**
     * 获取当前登录用户ID
     * @return int|string
     */
    public static function getId()
    {
        return \Yii::$app->user->identity->getId();
    }

    /**
     * 获取当前登录用户角色和权限
     * @return mixed
     */
    public static function getCurrentUserAuthorizationInfo()
    {
        return \Yii::$app->user->identity->doGetAuthorizationInfo();
    }

    /**
     * 删除用户权限缓存
     */
    public static function clearCache()
    {
        RedisUtils::hdel(RedisKeys::USER_ROLES, self::getCurrentUser()->getId());
        RedisUtils::hdel(RedisKeys::USER_PERMISSIONS, self::getCurrentUser()->getId());
    }

    /**
     * 退出登录
     */
    public static function logout()
    {
        RedisUtils::hdel(RedisKeys::USER_LOGIN, self::getCurrentUser()->getId());
    }
}