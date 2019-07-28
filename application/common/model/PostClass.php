<?php

namespace app\common\model;

use think\Model;

class PostClass extends Model
{
    //
    public function getPostClassList()
    {
        // 获取文章分类列表
        return $this->field('id','classname')->where('status','1')->select();
    }
}
