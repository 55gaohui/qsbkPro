<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
        // 向当前client_id发送数据 
        return Gateway::sendToCurrentClient(json_encode(['type'=>'bind','data'=>$client_id]));   //向当前客服端发送消息
        // 向所有人发送
//        Gateway::sendToAll("$client_id login\r\n");
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $data)
   {
       /*
        if (Gateway::getUidByClientId($client_id)) return;
        $data = json_decode($data,true);
        // 非法参数
        if (!is_array($data) || !array_key_exists('type',$data) || !array_key_exists('token',$data) || $data['type'] !== 'bind' || empty($data['token'])) return;
        // 验证token合法性
        $user = Cache::get($data['token']);
        if (!$user) return Gateway::sendToCurrentClient(json_encode(['type'=>'bind','msg'=>'非法token，禁止操作','status'=>false]));
        // 获取用户id
        $userId = array_key_exists('type',$user) ? $user['user_id'] : $user['id'];
        // 验证第三方是否绑定手机
        if ($userId < 1) return Gateway::sendToCurrentClient(json_encode(['type'=>'bind','msg'=>'请先绑定手机','status'=>false]));
        $User = \app\common\model\User::find($userId);
        // 验证用户是否绑定手机
        if (!$User->phone) return Gateway::sendToCurrentClient(json_encode(['type'=>'bind','msg'=>'请先绑定手机','status'=>false]));
        // 验证用户状态
        if ($User->status == 0) return Gateway::sendToCurrentClient(json_encode(['type'=>'bind','msg'=>'当前用户被禁用','status'=>false]));
        // 绑定
        Gateway::bindUid($client_id,$userId);
        return Gateway::sendToCurrentClient(json_encode(['type'=>'bind','msg'=>'绑定成功','status'=>true]));
        // 向所有人发送
        //Gateway::sendToAll("$client_id said $message\r\n");
        // 向所有人发送 
      Gateway::sendToAll("$client_id said $message\r\n");*/
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
    public static function onClose($client_id)
    {
        // 向所有人发送
//       GateWay::sendToAll("$client_id logout\r\n");
    }
}
