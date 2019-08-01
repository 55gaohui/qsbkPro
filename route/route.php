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
    // 文章分类获取
    Route::get('postclass','api/:version.PostClass/index');
    // 话题分类获取
    Route::get('topicclass','api/:version.TopicClass/index');
    //热门话题获取
    Route::get('hottopic','api/:version.Topic/index');
    //获取指定话题分类下的话题列表
    Route::get('topicclass/:id/topic/:page','api/:version.TopicCLass/topic');
    //获取文章
    Route::get('post/:id','api/:version.Post/index');
    //获取指定话题下的文章列表
    Route::get('topic/:id/post/:page','api/:version.Topic/post');
});

// 验证Token
Route::group('api/:version/',function (){
    //退出登录
    Route::post('user/logout','api/:version.User/logout');
})->middleware(['ApiUserAuth']);

// 验证Token  手机号 状态
Route::group('api/:version/',function (){
    //退出登录
    Route::post('image/uploadmore','api/:version.Image/uploadMore');
    //发布文章
    Route::post('post/create','api/:version.Post/create');
})->middleware(['ApiUserAuth','ApiUserBindPhone','ApiUserStatus']);


