<?php

namespace app\models;

use app\components\libs\Page;

/**
 * This is the model class for table "v_login_log".
 *
 * @property int $id
 * @property string $username 用户名
 * @property string $location 登录地点
 * @property string $ip ip地址
 * @property string $system 操作系统
 * @property string $browser 浏览器
 * @property string $login_time 登录时间
 */
class LoginLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_login_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login_time'], 'safe'],
            [['username'], 'string', 'max' => 64],
            [['location', 'ip', 'system', 'browser'], 'string', 'max' => 50],
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
            'location' => 'Location',
            'ip' => 'Ip',
            'system' => 'System',
            'browser' => 'Browser',
            'login_time' => 'Login Time',
        ];
    }

    private static function parseWhere($search)
    {
        $where = ['and'];
        if (!empty($search['username'])) {
            $where[] = ['like', 'username', $search['username']];
        }
        if (!empty($search['loginTimeStart'])) {
            $where[] = ['>=', 'login_time', $search['loginTimeStart']];
        }
        if (!empty($search['loginTimeEnd'])) {
            $where[] = ['<=', 'login_time', $search['loginTimeEnd']];
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
