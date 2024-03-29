<?php

namespace app\api\controller\v1;
use app\common\controller\BaseController;
use app\common\model\TopicClass as TopicClassModel;
use app\common\validate\TopicClassValidate;
use think\Controller;
use think\Request;

class TopicClass extends BaseController
{
    // 获取话题分类列表
    public function index()
    {
        $list = (new TopicClassModel())->getTopicClassList();
        return self::showResCode('获取成功',['list'=>$list]);
    }
    // 获取指定话题分类下的话题列表
    public function topic()
    {
        // 验证分类id和分页数
        (new TopicClassValidate())->goCheck();
        $list = (new TopicClassModel)->getTopic();
        return self::showResCode('获取成功',['list'=>$list]);
    }
}
