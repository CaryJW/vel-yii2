<?php

$defaultParams = require __DIR__ . '/../config/default.params.php';
$proParams = require __DIR__ . '/params/params.prod.php';
$params = array_merge($defaultParams, $proParams);
$db = require __DIR__ . '/db/db.production.php';

return [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    // 默认路由
    'defaultRoute' => 'site/index',
    // 控制器映射
    'controllerMap' => [
        'admin-user' => [
            'class' => 'app\controllers\sys\AdminUserController',
        ],
        'permission' => [
            'class' => 'app\controllers\sys\PermissionController',
        ],
        'role' => [
            'class' => 'app\controllers\sys\RoleController',
        ],
        'task' => [
            'class' => 'app\controllers\sys\TaskController',
        ],
        'dict' => [
            'class' => 'app\controllers\sys\DictController',
        ],
        'configure' => [
            'class' => 'app\controllers\sys\ConfigureController',
        ],
        'log' => [
            'class' => 'app\controllers\monitor\LogController',
        ],
        'login-log' => [
            'class' => 'app\controllers\monitor\LoginLogController',
        ],
        'active-user' => [
            'class' => 'app\controllers\monitor\ActiveUserController',
        ],
        'file-operation' => [
            'class' => 'app\controllers\sys\UploadController',
        ],
        'other' => [
            'class' => 'app\controllers\business\OtherController',
        ],
        'cron' => [
            'class' => 'app\controllers\business\CronController',
        ],
        'mail' => [
            'class' => 'app\controllers\sys\MailController',
        ],
    ],
    // 跨域处理
//    'as cors' => [
//        'class' => 'yii\filters\Cors',
//        'cors' => [
//            'Origin' => ['*'],
//            'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
//            'Access-Control-Request-Headers' => ['*'],
//        ],
//    ],
    'components' => [
        // 路由美化 https://segmentfault.com/a/1190000021285811
        'urlManager' => [
            'class' => \yii\web\UrlManager::class,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'suffix' => '',
            // 配置路径参数
            'rules' => [
                //  https://stackoverflow.com/questions/32988996/url-not-accepting-alpha-numeric-parameter-yii2-app-basic
                // '<controller>/<action>/<id:\d+>' => '<controller>/<action>',
                'permission/delete/<id:\d+>' => 'permission/delete',
                'permission/role/<id:\d+>' => 'permission/role',
                'dict/delete/<id:\d+>' => 'dict/delete',
                'dict/data/list' => 'dict/data-list',
                'dict/data/add' => 'dict/data-add',
                'dict/data/update' => 'dict/data-update',
                'dict/data/delete/<id:\d+>' => 'dict/data-delete',
                'dict/data/<types:\w+>' => 'dict/data-by-type',
                'dict/data/<type:\w+>/value/<value:\w+>' => 'dict/data-by-type-and-value',
                'log/delete/<id:\d+>' => 'log/delete',
                'login-log/delete/<id:\d+>' => 'login-log/delete',
                'active-user/kickout/<userId:\d+>' => 'active-user/kickout',
                'configure/get-by-value/<value:\S+>' => 'configure/get-by-value',
            ],
        ],
        // 请求配置
        'request' => [
            // cookie加密密钥
            'cookieValidationKey' => '_sVIrARW8v4G0q1gUN-_R67y95jM_awe',
            // 关闭cookie验证
            'enableCookieValidation' => false,
            # 关闭csrf验证
            'enableCsrfValidation' => false,
        ],
        // 配置响应数据为json格式
        'response' => [
            'class' => 'app\components\configure\Response',
            'format' => yii\web\Response::FORMAT_JSON,
        ],
        // redis 配置
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ],
        // jwt 配置
        'jwt' => [
            'class' => 'sizeg\jwt\Jwt',
            'key' => $params['jwt']['secret'],
        ],
        // 登录授权配置
        'user' => [
            'identityClass' => 'app\models\AdminUser',
            // 禁用cookie
            'enableAutoLogin' => false,
            // 禁用session
            'enableSession' => false,
            // 显示一个HTTP 403 错误而不是跳转到登录界面.
            'loginUrl' => null
        ],
        // 异常绑定
        'errorHandler' => [
            'class' => 'app\components\handler\GlobalExceptionHandler',
        ],
        // 验证码
        'captcha' => [
            'class' => 'app\components\configure\CaptchaAction',
            'maxLength' => 4,
            'minLength' => 4,
            'height' => 48,
            'width' => 150,
            'offset' => 15,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // false发送邮件，true只是生成邮件在runtime文件夹下，不发邮件
            'useFileTransport' => false,
            'viewPath' => '@components/mail',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.qq.com',
                'username' => '164****369@qq.com',
                'password' => 'km******bhe',
                'port' => '465',
                'encryption' => 'ssl',
            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['164****369@qq.com' => 'Cary'],
            ],
        ],
        // 日志配置
        'log' => [
            'traceLevel' => 1,
            'targets' => [
                [
                    'class' => 'app\components\configure\CustomFileLog',
                    'logFile' => dirname(__DIR__) . '/logs/' . PROJECT_NAME . '/info/' . date('Y-m-d') . '.log',
                    'levels' => [
                        'info'
                    ],
                    // 追加上下文信息，默认YII会包含PHP全局变量，这里我们不需要，设为空。
                    'logVars' => [],
                    // 只记录应用日志
                    'categories' => ['application'],
                    'exportInterval' => 0
                ],
                [
                    'class' => 'app\components\configure\CustomFileLog',
                    'logFile' => dirname(__DIR__) . '/logs/' . PROJECT_NAME . '/error/' . date('Y-m-d') . '.log',
                    'levels' => [
                        'error'
                    ],
                    // 追加上下文信息，默认YII会包含PHP全局变量，这里我们不需要，设为空。
                    'logVars' => [],
                    // 只记录应用日志
                    'categories' => ['application'],
                    'exportInterval' => 0
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];
