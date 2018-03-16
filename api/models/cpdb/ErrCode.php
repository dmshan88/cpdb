<?php
namespace app\models\cpdb;

class ErrCode
{
    const OK = 0;
    const DATABASE_ERR = 100;
    const PARAM_ERR = 101;
    const FUNC_RET_FALSE = 102;
    const INNER_ERR = 103;
    const UNKNOW_ERR = 104;
    const KEY_WRONG = 200;
    const INPUT_ERROR = 300;
    const ORDER_EXIST = 301;

    public static $errCode=[
        self::OK => 'ok',
        self::DATABASE_ERR => 'database error',
        self::PARAM_ERR => 'parameter error',
        self::FUNC_RET_FALSE => 'function return false',
        self::INNER_ERR => 'inner error',
        self::UNKNOW_ERR => 'unknown error',
        self::KEY_WRONG => 'auth key wrong',
        self::INPUT_ERROR => 'input error',
        self::ORDER_EXIST => 'order exist',
    ];
    public static function getErrText($err) {
        if (isset(self::$errCode[$err])) {
            return self::$errCode[$err];
        }else {
            return false;
        };
    }
    public function retErr($err = '')
    {
        if (isset(self::$errCode[$err])) {
            return [
                'errcode' => $err,
                'errmsg' => self::getErrText($err),
            ];
        }else {
            return false;
        };
    }
}