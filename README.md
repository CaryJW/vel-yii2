# vel-yii2

![https://img.shields.io/badge/php-7.3.4-blue](https://img.shields.io/badge/php-7.3.4-blue)
![https://img.shields.io/badge/yii-2.0.43-green](https://img.shields.io/badge/yii-2.0.43-green)

给 [vel-admin](https://gitee.com/flyxiaozhu/vel-admin) 后台提供 api

## PHP版本

8 > php >= 7

## sql文件

````
docs\vel.sql
docs\permissions.sql
````

## 安装依赖

````
composer install
````

## 相关配置

### 1. 控制器映射

控制器进行分组

````php
'controllerMap' => [
    'admin-user' => [
        'class' => 'app\controllers\sys\AdminUserController',
    ],
    'permission' => [
        'class' => 'app\controllers\sys\PermissionController',
    ],
],
````

### 2. jwt配置

````php
'jwt' => [
    // 密钥
    'secret' => '6sqGjmX3m6R4TMAVIQ+GmNdJJuGxzCp3FMeOn3WSVF0=',
    // 过期时间 两小时 单位秒
    'expireTime' => 7200,
],
````

### 2. 免认证的路径配置

````php
'allowGuestAction' => [
    'site/login',
    'site/test',
    'site/captcha',
],
````

### 3. redis key 前缀

````php
'redis' => [
    'prefix' => 'vel:'
],
````

### 3. 权限配置

````php
'permissions' => [
    'admin-user/add' => '_sys'
],
````

### 4. 跨域处理

#### 1) nginx

````
http {
...

# 跨域处理
# 接受任意域名的请求
add_header Access-Control-Allow-Origin *;
# 表明服务器支持的所有头信息字段
add_header Access-Control-Allow-Headers X-Requested-With;
# 表明服务器支持的所有跨域请求的方法
add_header Access-Control-Allow-Methods GET,POST,OPTIONS,PUT,DELETE;

...
}
````

#### 2) 配置文件添加，与components同级

````
'as cors' => [
        'class' => 'yii\filters\Cors',
        'cors' => [
            'Origin' => ['*'],
            'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
            'Access-Control-Request-Headers' => ['*'],
        ],
    ],
````

#### 3) BaseController

````
$behaviors['corsFilter'] = [
    'class' => Cors::class,
    'cors' => [
        'Origin' => ['*'],
        'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        'Access-Control-Request-Headers' => ['*'],
    ]
];
````

### 5. 配置日志

````
/**
     * 绑定行为
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 记录日志
        $behaviors['logFilter'] = [
            'class' => LogFilter::class,
            'optional' => [
                'tree' => '获取菜单栏',
            ]
        ];
        return $behaviors;
    }
````

### 5. 路径参数配置

````
'components' => [
    'urlManager' => [
        ...
        // 配置路径参数
        'rules' => [
            'permission/delete/<id:\d+>' => 'permission/delete',
            'permission/role/<id:\d+>' => 'permission/role',
        ],
    ],
]
````

### 6. 部署

将前端打包文件放入`web`目录下

### 7. 设置项目运行环境

nginx

- 测试环境：test
- 生产环境：production

````
location ~ \.php(.*)$ {
    ......
    fastcgi_param  APPLICATION 'production';
    ......
}
````