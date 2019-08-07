<?php

namespace app\api\controller\v1;

use app\common\controller\BaseController;
use app\common\model\Post as PostModel;
use app\common\validate\PostValidate;
use think\Request;

class Post extends BaseController
{
    //发布文章
    public function create(){
        (new PostValidate())->goCheck('create');
        $data = (new PostModel())->createPost();
        return self::showResCode('发布成功',['detail'=>$data]);
    }
    //获取文章详情
    public function index(){
        (new PostValidate())->goCheck('detail');
        $detail = (new PostModel())->getPostDetail();
        return self::showResCode('获取成功',['detail'=>$detail]);
    }
    //获取文章评论列表
    public function comment()
    {
        //验证文章id
        (new PostValidate())->goCheck('detail');
        $list = (new PostModel())->getComment();
        return self::showResCode('获取成功',['list'=>$list]);
    }

}
