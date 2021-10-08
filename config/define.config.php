<?php
// 读取nginx或apache配置的环境变量APPLICATION：development / production / test
if (isset($_SERVER['APPLICATION']) && $_SERVER['APPLICATION'] == 'production') {
    defined('YII_ENV') or define('YII_ENV', 'prod');
    defined('YII_DEBUG') or define('YII_DEBUG', false);

} elseif (isset($_SERVER['APPLICATION']) && $_SERVER['APPLICATION'] == 'test') {
    defined('YII_ENV') or define('YII_ENV', 'test');
    defined('YII_DEBUG') or define('YII_DEBUG', false);

} else {
    defined('YII_ENV') or define('YII_ENV', 'dev');
    defined('YII_DEBUG') or define('YII_DEBUG', true);
}

define('CURRENT_TIMESTAMP', $_SERVER['REQUEST_TIME']);
define('CURRENT_DATETIME', date('Y-m-d H:i:s'));
define('CURRENT_DATE', date('Y-m-d'));
const PROJECT_NAME = 'vel';
