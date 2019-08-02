<?php

namespace app\common\model;

use think\Model;

class Post extends Model
{
    //自动写入时间戳
    protected $autoWriteTimestamp = true;
    // 关闭自动写入update_time字段
    protected $updateTime = false;
    //关联图片表
    public function images(){
        return $this->belongsToMany('Image','post_image');
    }
    // 关联用户表
    public function user(){
        return $this->belongsTo('User');
    }

    public function share(){
        return $this->belongsTo('Post','share_id','id');
    }
    //发布文章
    public function createPost()
    {
        // 获取所有参数
        $params = request()->param();
        $userModel = new User();
        // 获取用户id
        $user_id = request()->userId;
        $currentUser = $userModel->get($user_id);
        $path = $currentUser->userinfo->path;
        // 发布文章
        $title = mb_substr($params['text'],0,30);
        $post = $this->create([
           'user_id'=>$user_id,
            'title'=>$title,
            'titlepic'=>'',
            'content'=>$params['text'],
            'path'=>$path ? $path : '未知',
            'type'=> 0,
            'post_class_id'=> $params['post_class_id'],
            'share_id'=> 0,
            'is_open'=> $params['isopen']
        ]);
        if(!$post) TApiException(200,'发布失败',10000);
        // 关联图片
        $imageLength = count($params['imglist']);
        if($imageLength > 0){
            $ImageModel = new Image();
            $imgidarr = [];
            for($i=0; $i<$imageLength; $i++){
                // 验证图片是否存在，是否是当前用户上传的
                $imagemodel = $ImageModel->isImageExist($params['imglist'][$i]['id'],$user_id);
                if($imagemodel){
                    // 设置第一张为封面图
                    if($i === 0){
                        $post->titlepic = getFileUrl($imagemodel->url);
                        $post->save();
                    }
                    $imgidarr[] = $params['imglist'][$i]['id'];
                }
            }
            // 发布关联
            if(count($imgidarr)>0) $post->images()->attach($imgidarr,['create_time'=>time()]);
        }
        return $post;
    }
    //获取文章
    public function getPostDetail()
    {
        $params = request()->param();
        return $this->with([
            'user'=>function($query){
                return $query->field('id,username,userpic');
            },
            'images'=>function($query){
                return $query->field('url');
            },
            'share'
        ])->select($params['id']);
    }

    //搜索文章
    public function search(){
        $params = request()->param();
        $list = $this->whereLike('title','%'.$params['keyword'].'%')->page($params['page'],10)->select();
        return $list;
    }
}
