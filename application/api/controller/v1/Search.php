<?php

namespace app\api\controller\v1;

use app\common\controller\BaseController;
use app\common\validate\SearchValidate;
use app\common\model\Topic as TopicModel;
use app\common\model\Post as PostModel;
use app\common\model\User as UserModel;
use think\Request;

class Search extends BaseController
{
    //搜索话题
    public function topic(){
        (new SearchValidate())->goCheck();
        $list = (new TopicModel())->search();
        return self::showResCode('搜索成功',['list'=>$list]);
    }

    //搜索文章
    public function post(){
        (new SearchValidate())->goCheck();
        $list = (new PostModel())->search();
        return self::showResCode('搜索成功',['list'=>$list]);
    }

    //搜索用户
    public function user(){
        (new SearchValidate())->goCheck();
        $list = (new UserModel())->search();
        return self::showResCode('搜索成功',['list'=>$list]);
    }
}
