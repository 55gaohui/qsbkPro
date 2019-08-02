<?php

namespace app\api\controller\v1;

use app\common\controller\BaseController;
use app\common\validate\AdsenseValidate;
use app\common\model\Adsense as AdsenseModel;
use think\Request;

class Adsense extends BaseController
{
    public function index(){
        (new AdsenseValidate())->goCheck();
        $list = (new AdsenseModel())->getList();
        return self::showResCode('获取成功',['list'=>$list]);
    }
}
