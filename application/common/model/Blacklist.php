<?php

namespace app\common\model;

use think\Model;

class Blacklist extends Model
{
    //自动写入时间
    protected $autoWriteTimestamp = true;

    //添加黑名单
    public function addBlack()
    {
        //获取所有参数
        $params = request()->param();
        //获取用户Id
        $userid = request()->userId;
        //不能拉黑自己
        if($params['id'] == $userid) TApiException(200,'非法操作',50000);
        $arr = ['user_id'=>$userid,'black_id'=>$params['id']];
        // 已经存在该记录
        if($this->where($arr)->find()) TApiException(200,'对方已被你拉黑过',40001);
        // 直接创建
        if (!$this->create($arr)) TApiException();
        return true;
    }

    //移除黑名单
    public function removeBlack()
    {
        //获取所有参数
        $params = request()->param();
        //获取用户Id
        $userid = request()->userId;
        $blackid = $params['id'];
        //不能操作自己
        if($userid == $blackid) TApiException(200,'非法操作',50000);
        $black = $this->where([
            'user_id'=>$userid,
            'black_id'=>$blackid
        ])->find();
        // 记录不存在
        if(!$black) TApiException(200,'对方已不在你的黑名单内',40002);
        // 直接删除
        if(!$black->delete()) TApiException();
        return true;
    }
}
