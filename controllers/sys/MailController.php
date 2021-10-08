<?php

namespace app\controllers\sys;

use app\components\libs\AppException;
use app\components\libs\ResultData;
use app\components\mail\MailService;
use app\controllers\BaseController;

/**
 * 邮件通知控制器
 *
 * @author Cary
 * @date 2021/10/8
 */
class MailController extends BaseController
{
    /**
     * @throws AppException
     */
    function actionSendCaptcha()
    {
        list($addressee, $verifyCode) = $this->getParams('addressee', 'verifyCode');
        $subject = "vel验证码通知";
        MailService::getInstance()->sendCaptcha($addressee, $subject, $verifyCode);
        return ResultData::ok();
    }
}