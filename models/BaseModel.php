<?php

namespace app\models;

use app\components\configure\TimeBehavior;
use app\components\utils\VelUtils;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

class BaseModel extends ActiveRecord
{
    /**
     * typecast
     * https://www.yiichina.com/doc/api/2.0/yii-behaviors-attributetypecastbehavior
     * 自动类型转换：
     * - 在成功通过模型验证之后
     * - 在模型保存之前（插入或者更新）
     * - 在模型查找之后（通过查询语句找到模型或模型执行刷新）
     *
     * TimeBehavior
     * 自动维护时间
     * @return string[][]
     */
    public function behaviors()
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
            ],
            [
                'class' => TimeBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['create_time', 'update_time'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['update_time'],
                ],
            ],
        ];
    }

    public function fields()
    {
        // 属性名转驼峰
        $fields = parent::fields();
        $values = array_values($fields);
        $keys = array_map(function ($kye) {
            return VelUtils::underscoreToCamel($kye);
        }, $values);
        return array_combine($keys, $values);
    }
}