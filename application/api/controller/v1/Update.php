<?php

namespace app\api\controller\v1;

use app\common\controller\BaseController;
use app\common\model\Update as UpdateModel;
use app\common\validate\UpdateValidate;
use think\Request;

class Update extends BaseController
{
    //检查更新
    public function update()
    {
        (new updateValidate())->goCheck();
        $list = (new UpdateModel())->appUpdate();
        return self::showResCode('获取成功',['list'=>$list]);

    }
}
