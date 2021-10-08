<?php

namespace app\models;

use app\components\libs\Page;

/**
 * This is the model class for table "v_dict_type".
 *
 * @property int $id
 * @property string $name 字典名称
 * @property string $type 字典类型
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class DictType extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_dict_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['create_time', 'update_time'], 'safe'],
            [['name', 'type'], 'string', 'max' => 125],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'type' => 'Type',
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
        if (!empty($search['name'])) {
            $where[] = ['like', 'name', $search['name']];
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
