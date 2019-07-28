<?php

namespace app\api\controller\v1;

use app\common\controller\BaseController;
use app\common\model\Topic as TopicModel;
use think\Controller;
use think\Request;

class Topic extends BaseController
{
    // 获取10个话题
    public function index()
    {
        $list = (new TopicModel())->gethotlist();
        return self::showResCode('获取成功',['list'=>$list]);
    }
}
