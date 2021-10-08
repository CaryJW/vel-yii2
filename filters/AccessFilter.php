<?php

namespace app\filters;

use app\components\authentication\UserHelper;
use app\components\libs\AppException;
use app\components\libs\ResultCode;
use yii\base\ActionFilter;

/**
 * 检查用户权限
 *
 * @author Cary
 * @date 2021/9/9
 */
class AccessFilter extends ActionFilter
{
    public $permissions;

    /**
     * @throws AppException
     */
    public function beforeAction($action)
    {
        $moduleId = $action->controller->module->id;
        $actionPath = ($moduleId == 'basic' ? '' : $moduleId . '/') . $action->controller->id . '/' . $action->id;

        if (array_key_exists($actionPath, $this->permissions)) {
            $perm = $this->permissions[$actionPath];
            $authorizationInfo = UserHelper::getCurrentUserAuthorizationInfo();
            $userPermission = $authorizationInfo->stringPermissions;
            if (!in_array($perm, $userPermission)) {
                throw new AppException(ResultCode::UNAUTHORIZED, '您没有访问权限');
            }
        }
        return true;
    }
}