<?php

namespace app\components\libs;

/**
 * redis键列表
 *
 * @author Cary
 * @date 2021/9/9
 */
class RedisKeys
{
    const USER_LOGIN = 'USER:LOGIN';
    const USER_ROLES = 'USER:ROLES';
    const USER_PERMISSIONS = 'USER:PERMISSIONS';
    const CAPTCHA_PREFIX = 'CAPTCHA:';
    const MENU = 'MENU';
    const USER_LOCK_LIMIT = 'USER:LOCK:LIMIT:';
}