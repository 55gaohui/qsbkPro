<?php

namespace app\common\model;

use think\Model;

class Post extends Model
{
    //自动写入时间戳
    protected $autoWriteTimestamp = true;
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
        $imageLength = count($params['imglist']);
        if($imageLength > 0){
            for($i=0; $i<$imageLength; $i++){
                if()
            }
        }
    }
}
