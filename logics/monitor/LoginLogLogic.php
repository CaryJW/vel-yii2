<?php

namespace app\logics\monitor;

use app\components\libs\AppException;
use app\components\libs\Page;
use app\components\libs\PageResult;
use app\components\libs\ResultCode;
use app\components\utils\QueryUtils;
use app\logics\BaseLogic;
use app\models\LoginLog;
use yii\db\StaleObjectException;

class LoginLogLogic extends BaseLogic
{
    public function findForPage($search, Page $page)
    {
        QueryUtils::parseParamsDate($search, 'loginTimeStart', 'loginTimeEnd');
        $pageResult = new PageResult();
        $pageResult->list = LoginLog::findForPage($search, $page);
        $pageResult->total = LoginLog::findCount($search);
        return $pageResult;
    }

    /**
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function delete($id)
    {
        $loginLog = LoginLog::findOne(['id' => $id]);
        if ($loginLog == null) {
            throw new AppException(ResultCode::ERROR, '无效ID');
        }
        $loginLog->delete();
    }
}