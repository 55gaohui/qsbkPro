<?php

namespace app\common\model;

use think\Model;

class Update extends Model
{
    //检查更新
    public function appUpdate()
    {
        $version = request()->param('ver');
        $res = $this->where('status',1)->order('create_time','desc')->find();
        //无记录
        if(!$res) \TApiException(200,'暂无版本更新');
        if($res['version'] == $version) \TApiException(200,'暂无版本更新');
        return $res;
    }
}
