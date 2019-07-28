<?php

namespace app\common\model;

use think\Model;

class Topic extends Model
{
    // 获取热门话题列表
    public function gethotlist()
    {
        return $this->where('type',1)->limit(10)->select()->toArray();
    }
}
