<?php

namespace app\api\controller\v1;

use app\common\controller\BaseController;
use app\common\model\Feedback as FeedbackModel;
use app\common\validate\FeedbackValidate;
use think\Request;

class Feedback extends BaseController
{
    //用户反馈
    public function feedback(){
        (new FeedbackValidate())->goCheck('feedback');
        (new FeedbackModel())->feedback();
        return self::showResCode('反馈成功');
    }
    //获取用户反馈列表
    public function feedbacklist(){
        (new FeedbackValidate())->goCheck('feedbacklist');
        $list = (new FeedbackModel())->feedbacklist();
        return self::showResCode('获取成功',['list'=>$list]);
    }
}
