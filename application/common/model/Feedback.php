<?php

namespace app\common\model;

use think\Model;

class Feedback extends Model
{
    //自动写入时间
    protected $autoWriteTimestamp = true;
    //用户反馈
    public function feedback(){
        //获取所有参数
        $params = request()->param();
        //获取用户ID
        $userid = request()->userId;
        $data = [
            'from_id' => $userid,
            'to_id' => 0,
            'data' => $params['data']
        ];
        if (!$this -> create($data)) return TApiException();
        return true;
    }
    //获取用户反馈列表
    public function feedbacklist()
    {
        //获取所有参数
        $page = request()->param('page');
        //获取用户ID
        $userid = request()->userId;
        return $this->where('from_id',$userid)->whereOr('to_id',$userid)->page($page,10)->select();

    }
}
