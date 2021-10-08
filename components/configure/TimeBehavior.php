<?php

namespace app\components\configure;

use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;

/**
 * 自动更新数据库时间字段
 *
 * @author Cary
 * @date 2021/9/30
 */
class TimeBehavior extends TimestampBehavior
{
    /**
     * @var bool 过滤null属性
     */
    public $filterNullAttribute = true;

    protected function getValue($event)
    {
        if ($this->value === null) {
            return CURRENT_DATETIME;
        }
        return parent::getValue($event);
    }

    public function evaluateAttributes($event)
    {
        if ($this->skipUpdateOnClean
            && $event->name == BaseActiveRecord::EVENT_BEFORE_UPDATE
            && empty($this->owner->dirtyAttributes)
        ) {
            return;
        }

        if (!empty($this->attributes[$event->name])) {
            $attributes = (array)$this->attributes[$event->name];
            $value = $this->getValue($event);
            foreach ($attributes as $attribute) {
                // ignore attribute names which are not string (e.g. when set by TimestampBehavior::updatedAtAttribute)
                if (is_string($attribute)) {
                    if ($this->filterNullAttribute && empty($this->owner->$attribute)) {
                        continue;
                    }
                    $this->owner->$attribute = $value;
                }
            }
        }
    }
}