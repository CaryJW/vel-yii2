<?php

namespace app\logics\monitor;

use app\components\libs\AppException;
use app\components\libs\Page;
use app\components\libs\PageResult;
use app\components\libs\ResultCode;
use app\components\utils\ExcelUtils;
use app\components\utils\QueryUtils;
use app\logics\BaseLogic;
use app\models\Log;
use Exception;
use yii\db\StaleObjectException;

class LogLogic extends BaseLogic
{
    public function findForPage($search, Page $page)
    {
        QueryUtils::parseParamsDate($search, 'createTimeStart', 'createTimeEnd');
        $pageResult = new PageResult();
        $pageResult->list = Log::findForPage($search, $page);
        $pageResult->total = Log::findCount($search);
        return $pageResult;
    }

    /**
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function delete($id)
    {
        $log = Log::findOne(['id' => $id]);
        if ($log == null) {
            throw new AppException(ResultCode::ERROR, '无效ID');
        }
        $log->delete();
    }

    /**
     * @param $search
     * @param Page $page
     */
    public function export($search, Page $page)
    {
        $data = Log::getList($search, $page);
        $title = [
            'id' => 'ID',
            'username' => '操作用户',
            'operation' => '操作内容',
            'time' => '耗时(毫秒)',
            'method' => '操作方法',
            'params' => '方法参数',
            'ip' => '操作者ip',
            'location' => '操作地点',
            'type' => '日志类型',
            'create_time' => '创建时间'
        ];
        try {
            ExcelUtils::export('系统日志.xlsx', $title, $data);
        } catch (Exception $e) {
            \Yii::error(`系统日志导出失败：{$e->getMessage()}`);
        }
    }
}