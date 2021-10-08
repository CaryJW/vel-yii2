<?php

namespace app\controllers\sys;

use app\components\libs\AppException;
use app\components\libs\ResultData;
use app\controllers\BaseController;
use app\filters\LogFilter;
use app\logics\sys\DictLogic;
use yii\db\StaleObjectException;

/**
 * 字典控制器
 *
 * @author Cary
 * @date 2021/9/26
 * @property DictLogic $logic
 */
class DictController extends BaseController
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
                'add' => '添加字典类型',
                'update' => '修改字典类型',
                'delete' => '删除字典类型',
                'data-add' => '添加字典数据',
                'data-update' => '修改字典数据',
                'data-delete' => '删除字典数据',
            ]
        ];
        return $behaviors;
    }

    public function init()
    {
        parent::init();
        $this->logic = new DictLogic();
    }

    public function actionList()
    {
        $search = $this->request();
        $page = $this->buildPage();
        return ResultData::ok()->putPage($this->logic->findForPage($search, $page));
    }

    /**
     * @throws AppException
     */
    public function actionAdd()
    {
        $this->logic->add($this->postParams());
        return ResultData::ok();
    }

    /**
     * @throws AppException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionUpdate()
    {
        $this->logic->update($this->postParams());
        return ResultData::ok();
    }

    /**
     * @return ResultData
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionDelete()
    {
        $id = $this->getParams('id');
        $this->logic->delete($id);
        return ResultData::ok();
    }

    public function actionDataList()
    {
        $search = $this->request();
        $page = $this->buildPage();
        return ResultData::ok()->putPage($this->logic->findDataListForPage($search, $page));
    }

    /**
     * @throws AppException
     */
    public function actionDataAdd()
    {
        $this->logic->dataAdd($this->postParams());
        return ResultData::ok();
    }

    /**
     * @throws AppException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDataUpdate()
    {
        $this->logic->dataUpdate($this->postParams());
        return ResultData::ok();
    }

    /**
     * @throws StaleObjectException
     * @throws AppException
     * @throws \Throwable
     */
    public function actionDataDelete()
    {
        $id = $this->getParams('id');
        $this->logic->dataDelete($id);
        return ResultData::ok();
    }

    /**
     * @throws AppException
     */
    public function actionDataByType()
    {
        $types = $this->getParams('types');
        return ResultData::ok()->put('dictData', $this->logic->getDictDataByTypes($types));
    }

    /**
     * @throws AppException
     */
    public function actionDataByTypeAndValue()
    {
        $type = $this->getParams('type');
        $value = $this->getParams('value');
        return ResultData::ok()->put('dictData', $this->logic->getDictDataByTypeAndValue($type, $value));
    }
}