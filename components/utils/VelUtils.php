<?php

namespace app\components\utils;

/**
 * 常用工具
 *
 * @author Cary
 * @date 2021/9/11
 */
class VelUtils
{
    /**
     * 获取 Model 错误信息中的 第一条，无错误时 返回 null
     * @param $model
     * @return mixed|string|null
     */
    public static function getModelError($model)
    {
        $errors = $model->getErrors();// 得到所有的错误信息
        if (!is_array($errors)) {
            return '';
        }
        $firstError = array_shift($errors);
        if (!is_array($firstError)) {
            return '';
        }
        return array_shift($firstError);
    }

    /**
     * 批量unset数组的字段
     * @param array $array
     * @param array $fields 要unset的字段
     * @param boolean $reverse 反向
     */
    public static function multiUnset(&$array, $fields, $reverse = false)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        if ($reverse) {
            $keys = array_keys($array);
            $fields = array_diff($keys, $fields);
        }

        foreach ($fields as $field) {
            unset($array[$field]);
        }

        return $array;
    }

    /**
     * 批量驼峰转下划线
     * @param $array
     * @param $fields
     * @param false $reverse
     * @param string $separator
     * @return mixed
     */
    public static function batchCamelToUnderscore(&$array, $fields, $reverse = false, $separator = '_')
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        if ($reverse) {
            $keys = array_keys($array);
            $fields = array_diff($keys, $fields);
        }

        foreach ($fields as $field) {
            $value = $array[$field];
            unset($array[$field]);
            $array[self::camelToUnderscore($field, $separator)] = $value;
        }

        return $array;
    }

    /**
     * 驼峰转下划线
     * @param $value
     * @param string $separator
     * @return string
     */
    public static function camelToUnderscore($value, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $value));
    }

    /**
     *下划线转驼峰
     * @param $value
     * @param string $separator
     * @return string
     */
    public static function underscoreToCamel($value, $separator = '_')
    {
        $underscore_words = $separator . str_replace($separator, " ", strtolower($value));
        return ltrim(str_replace(" ", "", ucwords($underscore_words)), $separator);
    }

    public static function getSystemBrowserInfo()
    {
        $userAgent = \Yii::$app->request->getHeaders()->get('User-Agent');
        return [
            'system' => self::getSystem($userAgent),
            'browser' => self::getBrowser($userAgent),
        ];
    }

    private static function getSystem($userAgent)
    {
        $indexOfMac = stripos($userAgent, 'Mac OS X');
        $indexOfWindows = stripos($userAgent, 'Windows NT');
        $indexOfLinux = stripos($userAgent, 'Linux');
        $isMac = $indexOfMac != false;
        $isWindows = $indexOfWindows != false;
        $isLinux = $indexOfLinux != false;

        $os = '';
        if ($isMac) {
            $os = substr($userAgent, $indexOfMac, strlen('MacOS X xxxxxxxx'));
        } elseif ($isLinux) {
            $os = 'Linux';
        } elseif ($isWindows) {
            $os = 'Windows ';
            $version = substr($userAgent, $indexOfWindows + strlen('Windows NT'), strlen('Windows NTx.x'));
            switch (trim($version)) {
                case '5.0':
                    $os .= '2000';
                    break;
                case '5.1':
                    $os .= 'XP';
                    break;
                case '5.2':
                    $os .= '200.';
                    break;
                case '6.0':
                    $os .= 'Vista';
                    break;
                case '6.1':
                    $os .= '7';
                    break;
                case '6.2':
                    $os .= '8';
                    break;
                case '6.3':
                    $os .= '8.1';
                    break;
                case '10':
                    $os .= '10';
                    break;
                default:
            }
        }
        return $os;
    }

    private static function getBrowser($userAgent)
    {
        $indexOfIe = stripos($userAgent, 'MSIE');
        $indexOfIe11 = stripos($userAgent, 'rv:');
        $indexOfFf = stripos($userAgent, 'Firefox');
        $indexOfSogou = stripos($userAgent, 'MetaSr');
        $indexOfChrome = stripos($userAgent, 'Chrome');
        $indexOfSafari = stripos($userAgent, 'Safari');
        $indexOfWindows = stripos($userAgent, 'Windows NT');

        $isWindows = $indexOfWindows != false;
        $containIe = $indexOfIe != false || ($isWindows && ($indexOfIe11 != false));
        $containFf = $indexOfFf != false;
        $containSogou = $indexOfSogou != false;
        $containChrome = $indexOfChrome != false;
        $containSafari = $indexOfSafari != false;

        $browser = '';
        if ($containSogou) {
            if ($containIe) {
                $browser = '搜狗' . substr($userAgent, $indexOfIe, strlen('IE x.x'));
            } else if ($containChrome) {
                $browser = '搜狗' . substr($userAgent, $indexOfChrome, strlen('Chrome/xx'));
            }
        } else if ($containChrome) {
            $browser = substr($userAgent, $indexOfChrome, strlen('Chrome/xx'));
        } else if ($containSafari) {
            $indexOfSafariVersion = stripos($userAgent, 'Version');
            $browser = "Safari " . substr($userAgent, $indexOfSafariVersion, strlen('Version/x.x.x.x'));
        } else if ($containFf) {
            $browser = substr($userAgent, $indexOfFf, strlen('Firefox/xx'));
        } else if ($containIe) {
            if ($indexOfIe11 > 0) {
                $browser = 'IE 11';
            } else {
                $browser = substr($userAgent, $indexOfIe, strlen('IE x.x'));
            }
        }
        return str_replace('/', ' ', $browser);
    }

    /**
     * 获取当前系统时间(精确到毫秒)
     * @return float
     */
    public static function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    /**
     * 获取当前系统时间(精确到微秒)
     * @return float
     */
    public static function getMicrosecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000000);
    }

    /**
     * 递归处理参数
     * @param $params
     * @return string|array
     */
    public static function _requestParam($params)
    {
        if (is_array($params)) {
            $tmp = [];
            foreach ($params as $key => $param) {
                $tmp[$key] = self::_requestParam($param);
            }
            return $tmp;
        } else {
            return htmlspecialchars(trim($params), ENT_QUOTES);
        }
    }
}