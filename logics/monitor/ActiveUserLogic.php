<?php

namespace app\logics\monitor;

use app\components\authentication\UserHelper;
use app\components\libs\Constants;
use app\components\libs\RedisKeys;
use app\components\utils\RedisUtils;
use app\logics\BaseLogic;
use app\models\ActiveUser;
use app\models\AdminUser;

class ActiveUserLogic extends BaseLogic
{
    public function getList($username)
    {
        $login = RedisUtils::hgetall(RedisKeys::USER_LOGIN);
        $userIds = array_filter(array_values($login), function ($val, $key) {
            return $key % 2 == 0;
        }, ARRAY_FILTER_USE_BOTH);
        $users = AdminUser::findAll(['id' => $userIds]);

        $result = [];
        foreach ($users as $user) {
            if (!empty($username) && $user->username != $username) {
                continue;
            }
            $au = new ActiveUser();
            $au->userId = $user->id;
            $au->username = $user->username;
            $au->status = $this->getStatus($user->id);
            $au->current = false;
            if ($user->id == UserHelper::getId()) {
                $au->current = true;
            }
            $au->loginTime = $user->login_time;
            $result[] = $au;
        }
        return $result;
    }

    private function getStatus($userId)
    {
        $token = RedisUtils::hget(RedisKeys::USER_LOGIN, $userId);
        try {
            $t = \Yii::$app->jwt->loadToken($token);
        } catch (\Throwable $e) {
        }
        if ($t == null) {
            return Constants::ACTIVE_USER_STATUS_OFFLINE;
        }
        return Constants::ACTIVE_USER_STATUS_ONLINE;
    }
}