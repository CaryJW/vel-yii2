<?php

namespace app\models;

use app\components\libs\Page;

/**
 * This is the model class for table "v_log".
 *
 * @property int $id
 * @property string $username 操作用户
 * @property string|null $operation 操作内容
 * @property float $time 耗时(毫秒)
 * @property string|null $method 操作方法
 * @property string|null $params 方法参数
 * @property string|null $ip 操作者ip
 * @property string|null $location 操作地点
 * @property int $type 日志类型
 * @property string $create_time 创建时间
 */
class Log extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['operation', 'method', 'params'], 'string'],
            [['time'], 'number'],
            [['type'], 'integer'],
            [['create_time'], 'safe'],
            [['username', 'ip'], 'string', 'max' => 64],
            [['location'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'operation' => 'Operation',
            'time' => 'Time',
            'method' => 'Method',
            'params' => 'Params',
            'ip' => 'Ip',
            'location' => 'Location',
            'type' => 'Type',
            'create_time' => 'Create Time',
        ];
    }

    private static function parseWhere($search)
    {
        $where = ['and'];
        if (!is_null($search['type']) && $search['type'] != '') {
            $where[] = ['type' => $search['type']];
        }
        if (!empty($search['username'])) {
            $where[] = ['like', 'username', $search['username']];
        }
        if (!empty($search['createTimeStart'])) {
            $where[] = ['>=', 'create_time', $search['createTimeStart']];
        }
        if (!empty($search['createTimeEnd'])) {
            $where[] = ['<=>', 'create_time', $search['createTimeEnd']];
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

    public static function getList($search, Page $page)
    {
        $where = self::parseWhere($search);
        return self::find()
            ->where($where)
            ->orderBy($page->sort)
            ->asArray()
            ->all();
    }
}
