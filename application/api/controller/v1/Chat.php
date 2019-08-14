<?php

namespace app\api\controller\v1;

use app\common\controller\BaseController;
use app\common\validate\ChatValidate;
use GatewayWorker\Lib\Gateway;
use think\facade\Cache;
use think\Request;

class Chat extends BaseController
{
    public function send(Request $request){
        $params = $request->userId;
    }
}
