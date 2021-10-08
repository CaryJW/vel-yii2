<?php

namespace app\logics\sys;

use app\components\libs\AppException;
use app\components\libs\Page;
use app\components\libs\PageResult;
use app\components\libs\ResultCode;
use app\components\utils\VelUtils;
use app\logics\BaseLogic;
use app\models\DictData;
use app\models\DictType;
use yii\db\StaleObjectException;

class DictLogic extends BaseLogic
{
    public function findForPage($search, Page $page)
    {
        $pageResult = new PageResult();
        $pageResult->list = DictType::findForPage($search, $page);
        $pageResult->total = DictType::findCount($search);
        return $pageResult;
    }

    /**
     * @throws AppException
     */
    public function add($data)
    {
        $dict = new DictType();
        $dict->attributes = $data;
        if (!$dict->validate()) {
            throw new AppException(ResultCode::ERROR, VelUtils::getModelError($dict));
        }
        if (DictType::find()->where(['type' => $dict->type])->exists()) {
            throw new AppException(ResultCode::ERROR, '类型重复');
        }
        $dict->save();
    }

    /**
     * @throws AppException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function update($data)
    {
        $dict = DictType::findOne(['id' => $data['id']]);
        if ($dict == null) {
            throw new AppException(ResultCode::ERROR, '无效ID');
        }
        if (DictType::find()->where([
            'and',
            ['type' => $data['type']],
            ['!=', 'id', $dict->id]
        ])->exists()) {
            throw new AppException(ResultCode::ERROR, '类型重复');
        }
        $dict->attributes = $data;
        if (!$dict->validate()) {
            throw new AppException(ResultCode::ERROR, VelUtils::getModelError($dict));
        }
        $dict->update();
    }

    /**
     * @param $id
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function delete($id)
    {
        $dict = DictType::findOne(['id' => $id]);
        if ($dict == null) {
            throw new AppException(ResultCode::ERROR, '无效ID');
        }
        if (DictData::find()->where(['type' => $dict->type])->exists()) {
            throw new AppException(ResultCode::ERROR, '该字典下有数据，不能删除');
        }
        $dict->delete();
    }

    public function findDataListForPage($search, Page $page)
    {
        $pageResult = new PageResult();
        $pageResult->list = DictData::findForPage($search, $page);
        $pageResult->total = DictData::findCount($search);
        return $pageResult;
    }

    /**
     * @throws AppException
     */
    public function dataAdd($data)
    {
        $dictData = new DictData();
        $dictData->attributes = $data;
        if (!$dictData->validate()) {
            throw new AppException(ResultCode::ERROR, VelUtils::getModelError($dictData));
        }
        if (DictData::find()->where([
            'type' => $dictData->type,
            'value' => $dictData->value
        ])->exists()) {
            throw new AppException(ResultCode::ERROR, '值重复');
        }
        $dictData->save();
    }

    /**
     * @param $data
     * @throws AppException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function dataUpdate($data)
    {
        $dictData = DictData::findOne(['id' => $data['id']]);
        if ($dictData == null) {
            throw new AppException(ResultCode::ERROR, '无效ID');
        }
        if (DictData::find()->where([
            'and',
            ['type' => $data['type']],
            ['value' => $data['value']],
            ['!=', 'id', $dictData->id]
        ])->exists()) {
            throw new AppException(ResultCode::ERROR, '值重复');
        }
        $dictData->attributes = $data;
        if (!$dictData->validate()) {
            throw new AppException(ResultCode::ERROR, VelUtils::getModelError($dictData));
        }
        $dictData->update();
    }

    /**
     * @throws StaleObjectException
     * @throws \Throwable
     * @throws AppException
     */
    public function dataDelete($id)
    {
        $dictData = DictData::findOne(['id' => $id]);
        if ($dictData == null) {
            throw new AppException(ResultCode::ERROR, '无效ID');
        }
        $dictData->delete();
    }

    public function getDictDataByTypes($types)
    {
        $typeArr = explode(',', $types);
        $result = [];
        foreach ($typeArr as $type) {
            $dictDataList = DictData::find()->where(['type' => $type])->asArray()->all();
            $result[$type] = array_column($dictDataList, 'label', 'value');
        }
        return $result;
    }

    public function getDictDataByTypeAndValue($type, $value)
    {
        return DictData::findOne([
            'type' => $type,
            'value' => $value
        ]);
    }
}