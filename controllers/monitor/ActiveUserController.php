<?php

namespace app\controllers\monitor;

use app\components\libs\AppException;
use app\components\libs\RedisKeys;
use app\components\libs\ResultData;
use app\components\utils\RedisUtils;
use app\controllers\BaseController;
use app\filters\LogFilter;
use app\logics\monitor\ActiveUserLogic;

/**
 * ActiveUserController
 *
 * @author Cary
 * @date 2021/9/26
 * @property ActiveUserLogic $logic
 */
class ActiveUserController extends BaseController
{
    /**
     * 绑定行为
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 记录日志
        $behaviors['logFilter'] = [
            'class' => LogFilter::class,
            'optional' => [
                'kickout' => '踢出在线用户',
            ]
        ];
        return $behaviors;
    }

    public function init()
    {
        parent::init();
        $this->logic = new ActiveUserLogic();
    }

    public function actionList()
    {
        $list = $this->logic->getList($this->request('username'));
        return ResultData::ok()
            ->put('list', $list)
            ->put('total', count($list));
    }

    /**
     * @throws AppException
     */
    public function actionKickout()
    {
        $userId = $this->getParams('userId');
        RedisUtils::hdel(RedisKeys::USER_LOGIN, $userId);
        return ResultData::ok();
    }
}