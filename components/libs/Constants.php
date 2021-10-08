<?php

namespace app\components\libs;

/**
 * 全局常量定义
 *
 * @author Cary
 * @date 2021/9/9
 */
class Constants
{
    // 管理员用户状态
    const ADMIN_USER_STATUS_NORMAL = 0; // 正常
    const ADMIN_USER_STATUS_LOCK = 1;   // 锁定

    // 菜单类型
    const MENU_NAVBAR = 0;
    const MENU_BUTTON = 1;
    const MENU_LABEL = 2;

    // 在线用户状态
    const ACTIVE_USER_STATUS_OFFLINE = 0;
    const ACTIVE_USER_STATUS_ONLINE = 1;

    const VALID_FILE_TYPE = ['xls', 'xlsx', 'zip'];
}