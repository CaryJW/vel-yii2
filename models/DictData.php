<?php

namespace app\models;

use app\components\libs\Page;

/**
 * This is the model class for table "v_dict_data".
 *
 * @property int $id
 * @property int $sort 字典排序
 * @property string $label 字典标签
 * @property string $value 字典键值
 * @property string $type 字典类型
 * @property int $status 状态：0正常 1停用
 * @property string $extend 扩展
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class DictData extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_dict_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort', 'status'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['label', 'value', 'type'], 'string', 'max' => 125],
            [['extend'], 'string', 'max' => 625],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sort' => 'Sort',
            'label' => 'Label',
            'value' => 'Value',
            'type' => 'Type',
            'status' => 'Status',
            'extend' => 'Extend',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    private static function parseWhere($search)
    {
        $where = ['and'];
        if (!is_null($search['type']) && $search['type'] != '') {
            $where[] = ['type' => $search['type']];
        }
        if (!empty($search['label'])) {
            $where[] = ['label' => $search['label']];
        }
        if (!empty($search['value'])) {
            $where[] = ['value' => $search['value']];
        }
        if (!is_null($search['status']) && $search['status'] != '') {
            $where[] = ['status' => $search['status']];
        }
        return $where;
    }

    public static function findForPage($search, Page $page)
    {
        $where = self::parseWhere($search);
        return self::find()
            ->where($where)
            ->offset($page->offset)
            ->limit($page->limit)
            ->orderBy($page->sort)
            ->all();
    }

    public static function findCount($search)
    {
        $where = self::parseWhere($search);
        return self::find()
            ->where($where)
            ->count(1);
    }
}
