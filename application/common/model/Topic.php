<?php

namespace app\common\model;

use think\Model;

class Topic extends Model
{
    // 获取热门话题列表
    public function gethotlist()
    {
        return $this->where('type',1)->withCount(['post','todaypost'])->limit(10)->select()->toArray();
    }
    // 关联文章
    public function post(){
        return $this->belongsToMany('Post','topic_post');
    }
    // 关联今日文章
    public function todaypost(){
        return $this->belongsToMany('Post','topic_post')->whereTime('post.create_time', 'today');
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

    //标题搜索话题
    public function search(){
        $params = request()->param();
        $list = $this->whereLike('title','%'.$params['keyword'].'%')->page($params['page'],10)->select();
        return $list;
    }
}
