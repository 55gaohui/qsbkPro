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
        // 1. 验证数据是否合法
        (new ChatValidate())->goCheck('send');
        // 2. 组织数据
        $data = $this->resdata($request);
        $to_id = $request->to_id;
        // 3. 验证对方用户是否在线
        if(Gateway::isUidOnline($to_id)){
            //直接发送
            Gateway::sendToUid($to_id,json_encode($data));
            //写入数据库
            //返回发送成功
            return self::showResCodeWithOutData('ok');
        } 

        //不在线，写入消息队列
        //获取之前消息
        $Cache = Cache::get('userchat_'.$to_id);
        

    }
    //组织数据
    public function resdata($request)
    {
        return [
            'to_id'=>$request->to_id,
            'from_id'=>$request->userId,
            'from_username'=>$request->from_username,
            'from_userpic'=>$request->from_userpic,
            'type'=>$request->type,
            'data'=>$request->data,
            'time'=>time()
        ];
    }
}
