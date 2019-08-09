<?php

namespace app\common\model;

use think\Model;

class Feedback extends Model
{
    //自动写入时间
    protected $autoWriteTimestamp = true;
    //用户反馈
    public function feedback(){
        $params = request()->param();
        $userid = request()->userId;
        $data = [
            'from_id' => $userid,
            'to_id' => 0,
            'data' => $params['data']
        ];
        if (!$this -> create($data)) return TApiException();
        return true;
    }
}
