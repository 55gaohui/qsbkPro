<?php

namespace app\api\controller\v1;

use app\common\controller\BaseController;
use app\common\validate\SupportValidate;
use app\common\model\Support as SupportModel;
use think\Request;

class Support extends BaseController
{
    // 用户顶踩
    public function index(){
        (new SupportValidate())->goCheck();
        (new SupportModel())->UserSupportPost();
        return self::showResCode('操作成功');
    }
}
