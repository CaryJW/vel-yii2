<?php

namespace app\components\authentication;

use app\components\libs\AppException;
use app\components\libs\ResultCode;
use Yii;
use yii\base\Action;
use yii\helpers\StringHelper;
use yii\web\IdentityInterface;
use yii\web\Request;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\User;

/**
 * 授权拦截
 *
 * @author Cary
 * @date 2021/9/8
 */
class JwtHttpBearerAuth extends \sizeg\jwt\JwtHttpBearerAuth
{

    /**
     * @throws AppException|UnauthorizedHttpException
     */
    public function beforeAction($action)
    {
        if ($this->isOptional($action)) {
            return true;
        }

        $response = $this->response ?: Yii::$app->getResponse();

        $identity = $this->authenticate(
            $this->user ?: Yii::$app->getUser(),
            $this->request ?: Yii::$app->getRequest(),
            $response
        );

        if ($identity == null) {
            return false;
        }
        return true;
    }

    /**
     * 验证token
     * @param User $user
     * @param Request $request
     * @param Response $response
     * @return false|mixed|IdentityInterface|null
     * @throws AppException|UnauthorizedHttpException
     */
    public function authenticate($user, $request, $response)
    {
        $authorization = $request->getHeaders()->get('Authorization');
        if (empty($authorization)) {
            throw new UnauthorizedHttpException();
        }

        // 验证token
        $token = $this->loadToken($authorization);
        if ($token === null) {
            throw new AppException(ResultCode::AUTHORIZED_ERROR, '登录过期，请重新登录！');
        }

        if ($this->auth) {
            $identity = call_user_func($this->auth, $token, get_class($this));
        } else {
            $identity = $user->loginByAccessToken($token, get_class($this));
        }

        return $identity;
    }

    /**
     * 过滤白名单
     * @param Action $action
     * @return bool
     */
    protected function isOptional($action)
    {
        $moduleId = $action->controller->module->id;
        $actionPath = ($moduleId == 'basic' ? '' : $moduleId . '/') . $action->controller->id . '/' . $action->id;
        foreach ($this->optional as $pattern) {
            if (StringHelper::matchWildcard($pattern, $actionPath)) {
                return true;
            }
        }
        return false;
    }
}