<?php

namespace app\http\middleware;
use think\facade\Cache;

class ApiGetUserid
{
    public function handle($request, \Closure $next)
    {
        //获取头部信息
        $param = $request->header();
        //当前用户token 是否存在（是否登录）
        if(array_key_exists('token',$param)){
            $user = Cache::get($param['token']);
            if($user){
                $request->userId = array_key_exists('type',$user) ? $user['user_id'] : $user['id'];
            }
        }
        return $next($request);
    }
}
