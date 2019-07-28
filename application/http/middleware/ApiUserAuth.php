<?php

namespace app\http\middleware;

use think\facade\Cache;

class ApiUserAuth
{
    public function handle($request, \Closure $next)
    {
        //获取头部信息
        $param = $request->header();
        //不含taoken
        if(!array_key_exists('token',$param)) TApiException(200,'请登录后操作',20003);
        //当前用户token 是否存在（是否登录）
        $token = $param['token'];
        $user = Cache::get($token);
        //验证失败 （未登录或已过期）
        if(!$user) TApiException(200,'非法token，请重新登录',20003);
        //将token和userid这类常用参数放在Request中
        $request->userToken = $token;
        $request->userId = array_key_exists('type',$user) ? $user['user_id'] : $user['id'];
        $request->userTokenUserInfo = $user;
        return $next($request);
    }
}
