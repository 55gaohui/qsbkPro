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
    //获取分类下的文章列表
    Route::get('postclass/:id/post/:page','api/:version.PostClass/post')->middleware(['ApiGetUserid']);
    //获取指定用户下的文章列表
    Route::get('user/:id/post/:page','api/:version.User/post');
    //搜索话题
    Route::post('search/topic','api/:version.Search/topic');
    //搜索文章
    Route::post('search/post','api/:version.Search/post');
    //搜索用户
    Route::post('search/user','api/:version.Search/user');
    //广告列表
    Route::get('adsense/:type','api/:version.Adsense/index');
});

// 验证Token
Route::group('api/:version/',function (){
    //退出登录
    Route::post('user/logout','api/:version.User/logout');
    //绑定手机
    Route::post('user/bindphone','api/:version.User/bindphone');
    //绑定邮箱
    Route::post('user/bindemail','api/:version.User/bindemail');
    //绑定第三方
    Route::post('user/bindother','api/:version.User/bindother');
})->middleware(['ApiUserAuth']);

// 验证Token  手机号 状态
Route::group('api/:version/',function (){
    //退出登录
    Route::post('image/uploadmore','api/:version.Image/uploadMore');
    //发布文章
    Route::post('post/create','api/:version.Post/create');
    //获取当前用户下的文章列表
    Route::get('user/allpost/:page','api/:version.User/Allpost');
    //用户顶踩
    Route::post('support','api/:version.Support/index');
})->middleware(['ApiUserAuth','ApiUserBindPhone','ApiUserStatus']);


