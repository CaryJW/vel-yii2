<?php

namespace app\controllers;


use app\components\authentication\UserHelper;
use app\components\libs\AppException;
use app\components\libs\RedisKeys;
use app\components\libs\ResultData;
use app\components\upload\UploadFactory;
use app\logics\SiteLogic;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;

/**
 * 默认控制器
 *
 * @author Cary
 * @date 2021/9/8
 * @property SiteLogic $logic
 */
class SiteController extends BaseController
{
    public function init()
    {
        parent::init();
        $this->logic = new SiteLogic();
    }

    /**
     * 首页
     */
    public function actionIndex()
    {
        die('首页');
    }

    /**
     * 登录
     * @return ResultData
     * @throws AppException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionLogin()
    {
        list($key, $captcha, $username, $password) = $this->postParams('key', 'captcha', 'username', 'password');
        return ResultData::ok()->put('token', $this->logic->login($key, $captcha, $username, $password));
    }

    public function actionTest()
    {
        try {
            UploadFactory::getInstance()->delete('https://lets-sys-back.oss-cn-guangzhou.aliyuncs.com/test/video/615530c8d4574.png');
        } catch (\Exception $e) {
        }
    }

    /**
     * 验证码
     * @throws InvalidConfigException
     */
    public function actionCaptcha()
    {
        $key = RedisKeys::CAPTCHA_PREFIX . uniqid();
        return ResultData::ok()->put('key', $key)
            ->put('captcha', $this->logic->captcha($key));
    }

    /**
     * 获取当前登录用户信息
     * @return ResultData
     */
    public function actionCurrentUserInfo()
    {
        return ResultData::ok()->put('user', UserHelper::getCurrentUser())
            ->put('permissionInfo', UserHelper::getCurrentUserAuthorizationInfo());
    }

    /**
     * 退出登录
     * @return ResultData
     */
    public function actionLogout()
    {
        UserHelper::logout();
        return ResultData::ok();
    }
}
