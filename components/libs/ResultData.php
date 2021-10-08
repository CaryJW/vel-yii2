<?php

namespace app\components\libs;

use stdClass;

/**
 * 响应实体
 * @author Cary
 * @date 2021/9/7
 */
class ResultData
{
    const DEFAULT_MESSAGE_SUCCESS = "success";
    const DEFAULT_MESSAGE_FAILED = "failed";

    public $code;
    public $message;
    public $data;

    final private function __construct()
    {
        $this->code = ResultCode::ERROR;
        $this->data = new StdClass();
    }

    final private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function ok()
    {
        $resultData = new self();
        $resultData->code = ResultCode::SUCCESS;
        $resultData->message = self::DEFAULT_MESSAGE_SUCCESS;
        return $resultData;
    }

    public static function failCode($code)
    {
        return self::fail($code, self::DEFAULT_MESSAGE_FAILED);
    }

    public static function failMessage($message)
    {
        return self::fail(ResultCode::ERROR, $message);
    }

    public static function fail($code, $message)
    {
        $resultData = new self();
        $resultData->code = intval($code);
        $resultData->message = $message;
        return $resultData;
    }

    public function put($key, $value)
    {
        $this->data->$key = $value;
        return $this;
    }

    public function putPage(PageResult $pageResult)
    {
        $this->put('list', $pageResult->list)
            ->put('total', intval($pageResult->total));
        return $this;
    }
}