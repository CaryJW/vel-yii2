<?php

namespace app\components\mail;

use Exception;

/**
 * 邮件服务
 *
 * @author Cary
 * @date 2021/9/30
 */
class MailService
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 发送简单内容邮件
     * @param $addressee string 收件人
     * @param $subject string 主题
     * @param $content string 内容
     */
    public function sendSimpleMail($addressee, $subject, $content)
    {
        $mail = \Yii::$app->mailer->compose();
        $mail->setTo($addressee);
        $mail->setSubject($subject);
        $mail->setTextBody($content);
        try {
            $mail->send();
        } catch (Exception $e) {
            \Yii::error("[邮件服务]邮件发送失败：{$e->getMessage()}");
        }
    }

    public function sendHtmlMail($addressee, $subject, $view, $params = [])
    {
        $mail = \Yii::$app->mailer->compose($view, $params);
        $mail->setTo($addressee);
        $mail->setSubject($subject);
        try {
            $mail->send();
        } catch (Exception $e) {
            \Yii::error("[邮件服务]邮件发送失败：{$e->getMessage()}");
        }
    }

    /**
     * 发送验证码
     * @param $addressee string 收件人
     * @param $subject string 主题
     * @param $verifyCode string 验证码
     */
    public function sendCaptcha($addressee, $subject, $verifyCode)
    {
        $view = 'captchaEmailTemplate';
        $params = [
            'title' => $subject,
            'verifyCode' => $verifyCode
        ];
        self::sendHtmlMail($addressee, $subject, $view, $params);
    }
}