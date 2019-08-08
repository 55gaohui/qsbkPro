<?php

namespace app\api\controller\v1;

use app\common\controller\BaseController;
use app\common\validate\BlacklistValidate;
use app\common\model\Blacklist as BlacklistModel;
use think\Request;

class Blacklist extends BaseController
{
    //加入黑名单
    public function addBlack()
    {
        (new BlacklistValidate())->goCheck();
        (new BlacklistModel())->addBlack();
        return self::showResCode('加入黑名单成功');
    }

    //移除黑名单
    public function removeBlack()
    {
        (new BlacklistValidate())->goCheck();
        (new BlacklistModel())->removeBlack();
        return self::showResCode('移除黑名单成功');
    }
}
