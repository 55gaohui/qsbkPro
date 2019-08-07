<?php

namespace app\common\model;

use think\Model;

class Comment extends Model
{
    //自动写入时间戳
    protected $autoWriteTimestamp = true;
    // 关联用户表
    public function user(){
        return $this->belongsTo('User','user_id');
    }
    // 评论
    public function comment(){
        //获取所有参数
        $params = request()->param();
        //获取用户ID
        $userId = request()->userId;
        //评论创建入库
        $comment = $this->create([
            'user_id'=>$userId,
            'fid'=>$params['fid'],
            'data'=>$params['data'],
            'post_id'=>$params['post_id']
        ]);
        //评论成功
        if($comment){
            //当fid>0 表示回复某条评论，需修改被评论的评论数
            if($params['fid']>0){
                $fcomment = self::get($params['fid']);
                $fcomment->fnum = ['inc',1];
                $fcomment->save();
            }
            return true;
        }
        TApiException('评论失败');
    }
}
