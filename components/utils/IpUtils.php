<?php

namespace app\components\utils;

/**
 * 获取IP工具
 *
 * @author Cary
 * @date 2021/9/13
 */
class IpUtils
{
    const UNKNOWN = "unknown";

    /**
     * 获取 IP地址
     * 使用 Nginx等反向代理软件， 则不能通过 request.getRemoteAddr()获取 IP地址
     * 如果使用了多级反向代理的话，X-Forwarded-For的值并不止一个，而是一串IP地址，
     * X-Forwarded-For中第一个非 unknown的有效IP字符串，则为真实IP地址
     */
    public static function getIpAddr()
    {
        $headers = \Yii::$app->request->getHeaders();
        $ip = $headers->get('x-forwarded-for');
        if (empty($ip) || strcasecmp(self::UNKNOWN, $ip) == 0) {
            $ip = $headers->get('Proxy-Client-IP');
        }
        if (empty($ip) || strcasecmp(self::UNKNOWN, $ip) == 0) {
            $ip = $headers->get('WL-Proxy-Client-IP');
        }
        if (empty($ip) || strcasecmp(self::UNKNOWN, $ip) == 0) {
            $ip = \Yii::$app->request->getRemoteIP();
        }
        return '0:0:0:0:0:0:0:1' == $ip ? '127.0.0.1' : $ip;
    }
}