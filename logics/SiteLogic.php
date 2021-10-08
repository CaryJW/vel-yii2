<?php

namespace app\logics;

use app\components\libs\AppException;
use app\components\libs\Constants;
use app\components\libs\RedisKeys;
use app\components\libs\ResultCode;
use app\components\utils\AddressUtils;
use app\components\utils\IpUtils;
use app\components\utils\JwtUtils;
use app\components\utils\RedisUtils;
use app\components\utils\VelUtils;
use app\logics\sys\ConfigureLogic;
use app\models\AdminUser;
use app\models\LoginLog;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\web\Response;

/**
 * 登录逻辑类
 *
 * @author Cary
 * @date 2021/9/8
 */
class SiteLogic extends BaseLogic
{
    /**
     * 登录
     * @param $key
     * @param $captcha
     * @param $username
     * @param $password
     * @return string
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function login($key, $captcha, $username, $password)
    {
        $code = RedisUtils::get($key);
        if ($code == null) {
            throw new AppException(ResultCode::ERROR, "验证码已过期");
        }

        if (!Yii::$app->captcha->mValidate($captcha, $code)) {
            throw new AppException(ResultCode::ERROR, "验证码错误");
        }
        RedisUtils::del($key);
        $user = AdminUser::getByUsername($username);
        $password = md5($password);
        if ($user == null) {
            throw new AppException(ResultCode::ERROR, '账户不存在');
        }
        if (Constants::ADMIN_USER_STATUS_LOCK == $user->status) {
            $this->checkUnlockTime($user);
        }
        if ($password != $user->password) {
            $this->checkLockLimit($user);
        }
        $token = JwtUtils::generateToken($user->id);
        RedisUtils::hset(RedisKeys::USER_LOGIN, $user->id, $token);

        $user->login_time = CURRENT_DATETIME;
        $user->update();

        // 保存登录日志
        $ip = IpUtils::getIpAddr();
        $info = VelUtils::getSystemBrowserInfo();

        $loginLog = new LoginLog();
        $loginLog->username = $user->username;
        $loginLog->browser = $info['browser'];
        $loginLog->system = $info['system'];
        $loginLog->ip = $ip;
        $loginLog->location = AddressUtils::getCityInfo($ip);
        $loginLog->login_time = CURRENT_DATETIME;
        $loginLog->save();

        return $token;
    }

    /**
     * 生成验证码
     * @param $key
     * @return string
     * @throws InvalidConfigException
     */
    public function captcha($key)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $captcha = Yii::$app->captcha;
        $code = $captcha->getVerifyCode();
        RedisUtils::set($key, $code, ['EX', 60]);
        return $captcha->mRun($code);
    }

    const PASSWORD_STRATEGY = 'password-strategy';

    /**
     * @param AdminUser $user
     * @throws AppException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    private function checkLockLimit(AdminUser $user)
    {
        $configureLogic = new ConfigureLogic();
        $passwordStrategy = $configureLogic->getConfigureByValue(self::PASSWORD_STRATEGY);

        $key = RedisKeys::USER_LOCK_LIMIT . $user->id;
        $limit = RedisUtils::incr($key);
        if ($limit == 1) {
            RedisUtils::expire($key, $this->getExpireTime($passwordStrategy['failLoginTimeType'], $passwordStrategy['failLoginTime']));
        }
        if ($limit >= $passwordStrategy['failLoginCount']) {
            $user->unlock_time = $this->getUnlockTime($passwordStrategy['unlockTimeType'], $passwordStrategy['unlockTime']);
            $user->status = Constants::ADMIN_USER_STATUS_LOCK;
            $user->update();
            throw new AppException(ResultCode::ACCOUNT_LOCKED, '账号已被锁定,请联系管理员');
        }
        $sLimit = $passwordStrategy['failLoginCount'] - $limit;
        throw new AppException(ResultCode::ERROR, "密码错误，您还有{$sLimit}次机会重试！");
    }

    private function getExpireTime($type, $time)
    {
        switch ($type) {
            case  0:
                return $time * 60 * 60;
            case 1:
                return $time * 60;
            default:
                return $time;
        }
    }

    private function getUnlockTime($type, $time)
    {
        switch ($type) {
            case  0:
                return date('Y-m-d H:i:s', strtotime("+{$time} hours"));
            case 1:
                return date('Y-m-d H:i:s', strtotime("+{$time} minutes"));
            default:
                return date('Y-m-d H:i:s', strtotime("+{$time} seconds"));
        }
    }

    /**
     * @param AdminUser $user
     * @throws AppException
     */
    private function checkUnlockTime(AdminUser $user)
    {
        if (!empty($user) && strtotime($user->unlock_time) <= strtotime("now")) {
            $user->unlock_time = null;
            $user->status = Constants::ADMIN_USER_STATUS_NORMAL;
            return;
        }
        throw new AppException(ResultCode::ACCOUNT_LOCKED, '账号已被锁定,请联系管理员');
    }
}