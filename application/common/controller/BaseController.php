<?php

namespace app\common\controller;

use think\Controller;
use think\Request;

class BaseController extends Controller
{
    //api统一返回格式
    static public function showResCode($msg = '', $data = [], $code = 200){
        $res = [
            'msg'=> $msg,
            'data'=>$data
        ];
        return json($res,$code);
    }
    //api统一返回格式无数据
    static public function showResCodeWithOutData($msg = '',$code = 200){
        return self::showResCode($msg,'',$code);
    }
}
