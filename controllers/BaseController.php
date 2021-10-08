<?php

namespace app\controllers;

use app\components\authentication\JwtHttpBearerAuth;
use app\components\libs\AppException;
use app\components\libs\Page;
use app\components\libs\ResultCode;
use app\components\utils\QueryUtils;
use app\components\utils\VelUtils;
use app\filters\AccessFilter;
use yii\filters\Cors;
use yii\web\Controller;

/**
 * 基础控制器
 *
 * @author Cary
 * @date 2021/9/6
 */
class BaseController extends Controller
{
    // 抽象逻辑方法
    protected $logic;
    // 默认当前页
    protected $page = 1;
    // 默认分页大小
    protected $limit = 10;
    // 排序
    protected $sort = '+id';

    /**
     * 绑定行为
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 跨域处理
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];
        // 检查登录
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => \Yii::$app->params['allowGuestAction'],
        ];
        // 检查权限
        $behaviors['access'] = [
            'class' => AccessFilter::class,
            'permissions' => \Yii::$app->params['permissions'],
        ];
        return $behaviors;
    }

    /**
     * 批量获取GET参数
     * @param ...$params
     * @return array | string
     * @throws AppException
     */
    public function getParams(...$params)
    {
        if (count($params) == 0) {
            return \Yii::$app->request->get();
        }

        $paramArr = [];
        foreach ($params as $param) {
            $p = \Yii::$app->request->get($param);
            if (empty($p)) {
                throw new AppException(ResultCode::ERROR, `{$p}.参数错误`);
            }
            $paramArr[] = $p;
        }
        return count($paramArr) == 1 ? $paramArr[0] : $paramArr;
    }

    /**
     * 批量获取POST参数
     * @param ...$params
     * @return array
     * @throws AppException
     */
    public function postParams(...$params)
    {
        if (count($params) == 0) {
            return \Yii::$app->request->post();
        }

        $paramArr = [];
        foreach ($params as $param) {
            $p = \Yii::$app->request->post($param);
            if (empty($p)) {
                throw new AppException(ResultCode::ERROR, `{$p}.参数错误`);
            }
            $paramArr[] = $p;
        }
        return count($paramArr) == 1 ? $paramArr[0] : $paramArr;
    }


    /**
     * 从get或post获取单个参数，或设置默认值
     * @param string $param
     * @param null $value
     * @return array|mixed|null
     */
    public function request($param = '', $value = null)
    {
        $params = VelUtils::_requestParam(array_merge($_GET, $_POST));
        if (empty($param)) {
            return $params;
        }
        if (isset($params[$param])) {
            return $params[$param];
        } else {
            return $value;
        }
    }

    public function buildPage()
    {
        $page = $this->request('page', $this->page);
        $limit = $this->request('limit', $this->limit);
        $sort = $this->request('sort', $this->sort);

        $pageObj = new Page();
        $pageObj->offset = ($page - 1) * $limit;
        $pageObj->limit = $limit;
        $pageObj->sort = QueryUtils::parseSort($sort);

        return $pageObj;
    }
}