<?php

namespace app\components\configure;

use yii\base\InvalidConfigException;

/**
 * 原验证码框组件使用session验证code，并且图片没有转base64，故重写
 *
 * @author Cary
 * @date 2021/9/10
 */
class CaptchaAction extends \yii\captcha\CaptchaAction
{

    /**
     * 获取验证码
     * @param false $regenerate
     * @return string
     */
    public function getVerifyCode($regenerate = false)
    {
        return $this->generateVerifyCode();
    }

    /**
     * 校验验证码
     * @param string $input
     * @param string $code
     * @param false $caseSensitive
     * @return bool
     */
    public function mValidate($input, $code, $caseSensitive = false)
    {
        return $caseSensitive ? ($input === $code) : strcasecmp($input, $code) === 0;
    }

    /**
     * @param $code
     * @return string
     * @throws InvalidConfigException
     */
    public function mRun($code)
    {
        $image = $this->renderImage($code);
        return 'data:image/png;base64,' . base64_encode($image);
    }
}