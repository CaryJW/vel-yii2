<?php

namespace app\components\utils;

/**
 * 查询工具
 *
 * @author Cary
 * @date 2021/9/13
 */
class QueryUtils
{
    const PLUS = '+';

    /**
     * 解析时间查询
     * @param $params
     * @param $start
     * @param $end
     */
    public static function parseParamsDate(&$params, $start, $end)
    {
        if (!empty($params[$start])) {
            $params[$start] = $params[$start] . ' 00:00:00';
        }
        if (!empty($params[$end])) {
            $params[$end] = $params[$end] . ' 23:59:59';
        }
    }

    /**
     * 解析排序
     * @param $sort
     * @param bool $camelToUnderscore
     * @return string
     */
    public static function parseSort($sort, $camelToUnderscore = true)
    {
        $sortArr = explode(',', $sort);
        $newSortArr = array_map(function ($item) use ($camelToUnderscore) {
            $s = substr($item, 0, 1);
            $f = substr($item, 1);
            if ($camelToUnderscore) {
                $f = VelUtils::camelToUnderscore($f);
            }
            if ($s === self::PLUS) {
                return $f . ' asc';
            } else {
                return $f . ' desc';
            }
        }, $sortArr);
        return implode(',', $newSortArr);
    }
}