<?php

namespace app\components\utils;

use Ip2Region;

/**
 * 根据IP获取地址信息工具
 *
 * @author Cary
 * @date 2021/9/13
 */
class AddressUtils
{
    public static function getCityInfo($ip)
    {
        $ip2region = new Ip2Region();
        try {
            $info = $ip2region->btreeSearch($ip);
            return $info['region'];
        } catch (\Exception $e) {
            \Yii::error(`获取地址信息异常：{$e->getMessage()}`);
        }
        return "";
    }
}