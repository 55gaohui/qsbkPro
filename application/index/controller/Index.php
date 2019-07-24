<?php
namespace app\index\controller;

use app\common\controller\BaseController;
use app\lib\exception\BaseException;
use app\common\validate\UserValidate;
class Index extends BaseController
{
    public function index()
    {
//        (new UserValidate())->goCheck();
        $list = [
            ['id'=>1,'name'=>'小米'],
            ['id'=>2,'name'=>'小命']
        ];
        return self::showResCode('操作成功',['list'=>$list],200);
    }
}


