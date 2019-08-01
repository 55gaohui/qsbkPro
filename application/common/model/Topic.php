<?php

namespace app\common\model;

use think\Model;

class Topic extends Model
{
    // 获取热门话题列表
    public function gethotlist()
    {
        return $this->where('type',1)->limit(10)->select()->toArray();
    }
    // 关联文章
    public function post(){
        return $this->belongsToMany('Post','topic_post');
    }
    //获取指定话题下的文章（分页）
    public function getPost(){
        $params = request()->param();
        $posts = self::get($params['id'])->post()->with(['user'=>function($query){
            return $query->field('id,username,userpic');
        },'images'=>function($query){
            return $query->field('url');
        },'share'])->page($params['page'],10)->select();
        return $posts;
    }
}
