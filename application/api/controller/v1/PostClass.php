<?php

namespace app\api\controller\v1;
use app\common\model\PostClass as PostClassModel;
use app\common\controller\BaseController;

use think\Request;

class PostClass extends BaseController
{
    public function index()
    {
        // 获取文章分类列表
        $list = (new PostClassModel())->getPostClassList();
        return self::showResCode('获取成功',['list'=>$list] );
    }
}
