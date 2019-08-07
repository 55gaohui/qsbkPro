<?php

namespace app\common\model;

use think\Model;

class Adsense extends Model
{
    //获取广告列表
    public function getList(){
        $params = request()->param();
        $list = $this->where('type',$params['type'])->select();
        return $list;
    }
}
