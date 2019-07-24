<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 不验证Token
Route::group('api/:version/',function (){
    //发送验证码
    Route::post('user/sendcode','api/:version.User/sendCode');
    //手机登录
    Route::post('user/phonelogin','api/:version.User/phoneLogin');
    //用户密码登录
    Route::post('user/login','api/:version.User/login');
    //第三方登录
    Route::post('user/otherlogin','api/:version.User/otherLogin');
});

// 验证Token
Route::group('api/:version/',function (){
    //退出登录
    Route::post('user/logout','api/:version.User/logout');
})->middleware(['ApiUserAuth']);


