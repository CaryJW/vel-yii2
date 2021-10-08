<?php

namespace app\components\utils;

use yii\helpers\BaseJson;
use yii\redis\Connection;

/**
 * redis工具
 *
 * @author Cary
 * @date 2021/9/9
 */
class RedisUtils
{
    /**
     * 添加key前缀
     * @param $key
     * @return string
     */
    protected static function buildKey($key)
    {
        return \Yii::$app->params['redis']['prefix'] . $key;
    }

    /**
     * 获取redis实例
     * @return mixed|object|Connection|null
     */
    protected static function getRedis()
    {
        return \Yii::$app->redis;
    }

    /**
     * 判断key是否存在
     * @param $key
     * @return bool
     */
    public static function exists($key)
    {
        return self::getRedis()->exists(self::buildKey($key));
    }


    /**
     * 设置key
     * @param $key
     * @param $value
     * @param array $options
     * EX seconds -- 设置指定key的过期时间，以秒为单位
     * PX milliseconds -- 设置指定key的过期时间，以毫秒为单位
     * EXAT timestamp-seconds -- 设置指定key到期的指定 Unix 时间，以秒为单位
     * PXAT timestamp-milliseconds -- 设置指定key到期的指定 Unix 时间，以毫秒为单位
     * NX -- 只有在key不存在的情况下才设置.
     * XX -- 只有在key存在的情况下才设置.
     *
     * eg: ['NX', 'EX', '3600']
     * @return bool
     */
    public static function set($key, $value, array $options = [])
    {
        if (!is_string($value)) {
            $value = BaseJson::encode($value);
        }
        return self::getRedis()->set(self::buildKey($key), $value, ...$options);
    }

    /**
     * 设置过期时间（秒）
     * @param $key
     * @param $seconds
     * @return bool
     */
    public static function expire($key, $seconds)
    {
        return self::getRedis()->expire(self::buildKey($key), $seconds);
    }

    /**
     * 读取缓存
     * @param $key
     * @param bool $json_decode
     * @return mixed|object|Connection|null
     */
    public static function get($key, $json_decode = false)
    {
        $value = self::getRedis()->get(self::buildKey($key));
        if ($json_decode) {
            return BaseJson::decode($value);
        }
        return $value;
    }

    /**
     * 删除key
     * @param string $key
     * @param bool $strict
     * 是否使用严格模式
     * true: 当指定键不存在时，返回false
     * false: 当指定键不存在时，但会true
     * @return bool
     */
    public static function del($key, $strict = false)
    {
        // 非严格模式,当指定键不存在时，但会true
        if (!$strict && !self::exists($key)) {
            return true;
        };
        return self::getRedis()->del(self::buildKey($key));
    }

    /**
     * 查看key剩余存活时间
     * 大于等于0时，表示剩余过期秒数
     * -1 表示key存在，并且没有过期时间
     * -2 表示key已经不存在了
     *
     * 查看key的剩余过期时间
     * @param $key
     * @return int
     */
    public static function ttl($key)
    {
        return self::getRedis()->ttl(self::buildKey($key));
    }

    /**
     * 设置哈希key
     * @param $key
     * @param $field
     * @param $value
     * @return bool
     */
    public static function hset($key, $field, $value)
    {
        if (!is_string($value)) {
            $value = BaseJson::encode($value);
        }
        return self::getRedis()->hset(self::buildKey($key), $field, $value);
    }

    /**
     * 获取哈徐key字段
     * @param $key
     * @param $field
     * @param false $json_decode
     * @return mixed
     */
    public static function hget($key, $field, $json_decode = false)
    {
        $value = self::getRedis()->hget(self::buildKey($key), $field);
        if ($json_decode) {
            return BaseJson::decode($value);
        }
        return $value;
    }

    /**
     * 获取哈徐key字段
     * @param $key
     * @param $field
     * @param false $json_decode
     * @return mixed
     */
    public static function hgetall($key)
    {
        return self::getRedis()->hgetall(self::buildKey($key));
    }

    /**
     * 删除哈希key字段
     * @param $key
     * @param ...$field
     * @return bool
     */
    public static function hdel($key, ...$field)
    {
        return self::getRedis()->hdel(self::buildKey($key), ...$field);
    }

    /**
     * 哈希key字段是否村咋
     * @param $key
     * @param $field
     * @return bool
     */
    public static function hexists($key, $field)
    {
        return self::getRedis()->hexists(self::buildKey($key), $field);
    }

    /**
     * 向集合添加一个或多个成员
     * @param $key
     * @param $members
     * @return int 成功添加数量
     */
    public static function sadd($key, $members)
    {
        return self::getRedis()->sadd(self::buildKey($key), ...(array)$members);
    }

    /**
     * 移除集合中一个或多个成员
     * @param $key
     * @param $members
     * @return int
     */
    public static function srem($key, $members)
    {
        return self::getRedis()->srem(self::buildKey($key), ...(array)$members);
    }

    /**
     * 判断 member 元素是否是集合 key 的成员
     * @param $key
     * @param $member
     * @return bool
     */
    public static function sismember($key, $member)
    {
        return self::getRedis()->sismember(self::buildKey($key), $member);
    }

    /**
     * 返回集合中的所有成员
     * @param $key
     * @return array
     */
    public static function smembers($key)
    {
        return self::getRedis()->smembers(self::buildKey($key));
    }

    /**
     * 获取集合的成员数
     * @param $key
     * @return bool
     */
    public static function scard($key)
    {
        return self::getRedis()->scard(self::buildKey($key));
    }

    /**
     * 有序集合添加
     * @param $key
     * @param array $options
     * @return int
     */
    public static function zadd($key, array $options = [])
    {
        return self::getRedis()->zadd(self::buildKey($key), ...$options);
    }

    /**
     * 自增
     * @param $key
     * @return mixed
     */
    public static function incr($key)
    {
        return self::getRedis()->incr(self::buildKey($key));
    }

    /**
     * 自减
     * @param $key
     * @return mixed
     */
    public static function decr($key)
    {
        return self::getRedis()->decr(self::buildKey($key));
    }
}