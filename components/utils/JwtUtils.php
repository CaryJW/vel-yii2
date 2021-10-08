<?php

namespace app\components\utils;

/**
 * jwt工具
 *
 * @author Cary
 * @date 2021/9/8
 */
class JwtUtils
{
    /**
     * 生成token
     * @param $userId
     * @return string
     */
    public static function generateToken($userId)
    {
        $jwt = \Yii::$app->jwt;
        $time = time();
        return $jwt->getBuilder()
            ->issuedBy('Cary')                                                // 签发人
            ->issuedAt($time)                                                       // 签发时间
            ->expiresAt($time + \Yii::$app->params['jwt']['expireTime'])   // 过期时间
            ->withClaim('userId', $userId)                                    // 内容
            ->getToken($jwt->getSigner('HS256'), $jwt->getKey())                // 加密生成token对象
            ->__toString();                                                         // 调用魔术方法输出token字符串
    }

    /**
     * 获取数据
     * @param $token
     * @return mixed
     */
    public static function getUserIdByToken($token)
    {
        return $token->getClaim('userId');
    }
}