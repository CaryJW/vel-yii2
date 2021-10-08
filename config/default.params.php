<?php
return [
    // jwt配置
    'jwt' => [
        // 密钥
        'secret' => '6sqGjmX3m6R4TMAVIQ+GmNdJJuGxzCp3FMeOn3WSVF0=',
        // 过期时间 两小时 单位秒
        'expireTime' => 7200,
    ],
    // 免认证的路径配置
    'allowGuestAction' => [
        'site/login',
        'site/captcha',
        'site/test',
    ],
    // redis key 前缀
    'redis' => [
        'prefix' => 'vel:'
    ],
    // 权限配置
    'permissions' => [
        'admin-user/list' => 'admin-user:list',
        'admin-user/add' => 'admin-user:add',
        'admin-user/update' => 'admin-user:update',
        'admin-user/update-password' => 'password:update',
        'permission/add' => 'menu:add',
        'permission/update' => 'menu:update',
        'permission/delete' => 'menu:delete',
        'role/add' => 'role:add',
        'role/update' => 'role:update',
        'dict/list' => 'dict:list',
        'dict/add' => 'dict:add',
        'dict/update' => 'dict:update',
        'dict/delete' => 'dict:delete',
        'dict/data/list' => 'dict:data-list',
        'dict/data/add' => 'dict:data-add',
        'dict/data/update' => 'dict:data-update',
        'dict/data/delete' => 'dict:data-delete',
        'log/list' => 'sys-log:list',
        'log/delete' => 'sys-log:delete',
        'login-log/list' => 'login-log:list',
        'login-log/delete' => 'login-log:delete',
        'active-user/list' => 'online-user:list',
        'active-user/kickout' => 'online-user:kickout',
        'log/export' => 'sys-log:export',
    ],
    // 记录操作日志
    'aopLog' => true,
    // 定时器请求secret
    'cron-secret' => 'aAuwUu6L3qEPkSYXLU7IdIIi5pyYEpkI',
    // 文件上传方式 ['local', ali']
    'uploadType' => 'ali',
    // 上传文件允许最大大小：1G
    'allowImageSize' => 1048576,
];