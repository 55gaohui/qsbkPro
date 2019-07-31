<?php

namespace app\api\controller\v1;

use app\common\controller\BaseController;
use app\common\model\Post as PostModel;
use app\common\validate\PostValidate;
use think\Request;

class Post extends BaseController
{
    public function create(){
        (new PostValidate())->check('create');
        $data = (new PostModel())->createPost();
        return self::showResCode('发布成功',['detail'=>$data]);
    }
}
