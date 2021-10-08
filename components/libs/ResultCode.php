<?php

namespace app\components\libs;

/**
 * 错误代码管理
 *
 * @author Cary
 * @date 2021/9/7
 */
class ResultCode
{
    const SUCCESS = 20000;              // 成功状态码
    const ERROR = 20001;                // 失败状态码
    const HTTP_ERROR = 20002;           // http请求错误
    const AUTHORIZED_ERROR = 40000;     // 授权错误
    const UNAUTHORIZED = 40001;         // 未授权
    const ACCOUNT_LOCKED = 40002;       // 账户被锁定
    const SERVER_ERROR = 50000;         // 系统内部异常
}