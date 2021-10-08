<?php

namespace app\logics\sys;

use app\components\libs\AppException;
use app\components\libs\ResultCode;
use app\logics\BaseLogic;
use app\models\DictData;
use yii\db\StaleObjectException;
use yii\helpers\Json;

class ConfigureLogic extends BaseLogic
{
    const SYSTEM_CONFIGURE = 'SYSTEM_CONFIGURE';
    const PASSWORD_STRATEGY = "password-strategy";

    /**
     * @throws AppException
     */
    public function getConfigureByValue($value)
    {
        $dictData = DictData::findOne(['type' => self::SYSTEM_CONFIGURE, 'value' => $value]);
        if ($dictData == null) {
            throw new AppException(ResultCode::ERROR, '无效数据');
        }
        $result = Json::decode($dictData->extend);
        $passwordComplexity = array_map(function ($val) {
            return intval($val);
        }, $result['passwordComplexity']);
        $result['passwordComplexity'] = $passwordComplexity;
        $result['failLoginTimeType'] = intval($result['failLoginTimeType']);
        $result['unlockTimeType'] = intval($result['unlockTimeType']);
        return $result;
    }

    /**
     * @param $data
     * @throws AppException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function savePasswordStrategy($data)
    {
        $dictData = DictData::findOne(['type' => self::SYSTEM_CONFIGURE, 'value' => self::PASSWORD_STRATEGY]);
        if ($dictData == null) {
            throw new AppException(ResultCode::ERROR, '无效数据');
        }
        $dictData->extend = Json::encode($data);
        $dictData->update();
    }
}