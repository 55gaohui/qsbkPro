<?php

namespace app\api\controller\v1;

use app\common\controller\BaseController;
use app\common\validate\BaseValidate;
use app\common\model\Feedback as FeedbackModel;
use app\common\validate\FeedbackValidate;
use think\Request;

class Feedback extends BaseController
{
    //用户反馈
    public function feedback(){
        (new FeedbackValidate())->goCheck();
        (new FeedbackModel())->feedback();
        return self::showResCode('反馈成功');
    }
}
