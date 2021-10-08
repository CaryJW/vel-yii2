<?php

namespace app\filters;

use app\components\authentication\UserHelper;
use app\components\utils\AddressUtils;
use app\components\utils\IpUtils;
use app\components\utils\VelUtils;
use app\models\Log;
use yii\base\ActionFilter;
use yii\helpers\BaseJson;
use yii\helpers\StringHelper;

/**
 * 记录日志
 *
 * @author Cary
 * @date 2021/9/9
 */
class LogFilter extends ActionFilter
{
    // 执行时长 毫秒
    private $time;

    public $optional = [];

    public function beforeAction($action)
    {
        $this->time = VelUtils::getMillisecond();
        return true;
    }

    public function afterAction($action, $result)
    {
        if (\Yii::$app->params['aopLog'] && $this->isOptional($action->id)) {
            $moduleId = $action->controller->module->id;
            $actionPath = ($moduleId == 'basic' ? '' : $moduleId . '/') . $action->controller->id . '/' . $action->id;
            $ip = IpUtils::getIpAddr();
            $this->time = VelUtils::getMillisecond() - $this->time;

            $log = new Log();
            $log->username = UserHelper::getCurrentUser()->username;
            $log->ip = $ip;
            $log->time = $this->time;
            $log->operation = $this->optional[$action->id];
            $log->method = $actionPath;
            $log->location = AddressUtils::getCityInfo($ip);
            $log->params = $this->handleParams();
            $log->save();

        }

        return parent::afterAction($action, $result);
    }

    private function isOptional($actionId)
    {
        foreach (array_keys($this->optional) as $pattern) {
            if (StringHelper::matchWildcard($pattern, $actionId)) {
                return true;
            }
        }
        return false;
    }

    private function handleParams()
    {
        $params = VelUtils::_requestParam(array_merge($_GET, $_POST));
        return BaseJson::encode($params);
    }
}