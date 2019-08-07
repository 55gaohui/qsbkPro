<?php

namespace app\common\model;

use think\Model;

class Support extends Model
{
    //自动写入时间戳
    protected $autoWriteTimestamp = true;
    // 用户顶踩文章
    public function UserSupportPost(){
        //获取所有参数
        $params = request()->param();
        $userid = request()->userId;
        //判断用户是否顶踩过
        $support = $this->where(['user_id'=>$userid,'post_id'=>$params['post_id']])->find();
        // 已经顶踩过，判断当前操作是否相同
        if($support){
            if($support['type'] == $params['type']) TApiException(200,'请勿重复操作',40000);
            //修改顶踩操作
//            return self::update(['id'=>$support['id'],'type'=>$params['type']]);
            return $this->save(['type'=>$params['type']],['id'=>$support['id']]);
        }
        //未顶踩过
        return $this->create([
            'user_id'=>$userid,
            'post_id'=>$params['post_id'],
            'type'=>$params['type']
        ]);
    }
}
