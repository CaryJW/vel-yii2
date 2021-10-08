<?php
// 载入全局定义
require __DIR__ . '/../config/define.config.php';

// 注册 Composer 自动加载器
require __DIR__ . '/../vendor/autoload.php';
// 包含 Yii 类文件
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// 根据环境变量读取配置文件
if (YII_ENV_PROD) {
    $config = require __DIR__ . '/../config/production.php';
} elseif (YII_ENV_TEST) {
    $config = require __DIR__ . '/../config/test.php';
} else {
    $config = require __DIR__ . '/../config/development.php';
}

// 创建、配置、运行一个应用
(new yii\web\Application($config))->run();
