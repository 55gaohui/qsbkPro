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
    Route::get('topicclass/:id/topic/:page','api/:version.TopicClass/topic');
    //获取文章
    Route::get('post/:id','api/:version.Post/index');
    //获取指定话题下的文章列表
    Route::get('topic/:id/post/:page','api/:version.Topic/post')->middleware(['ApiGetUserid']);
    //获取分类下的文章列表
    Route::get('postclass/:id/post/:page','api/:version.PostClass/post')->middleware(['ApiGetUserid']);
    //获取指定用户下的文章列表
    Route::get('user/:id/post/:page','api/:version.User/post')->middleware(['ApiGetUserid']);
    //搜索话题
    Route::post('search/topic','api/:version.Search/topic');
    //搜索文章
    Route::post('search/post','api/:version.Search/post')->middleware(['ApiGetUserid']);
    //搜索用户
    Route::post('search/user','api/:version.Search/user');
    //广告列表
    Route::get('adsense/:type','api/:version.Adsense/index');
    //文章评论列表
    Route::get('post/:id/comment','api/:version.Post/comment');
    //检查更新
    Route::post('update','api/:version.Update/update');
    // 获取关注的人的公开文章列表
    Route::get('followpost/:page','api/:version.Post/followPost')->middleware(['ApiGetUserid']);
    //  获取指定用户详细信息
    Route::post('getuserinfo','api/:version.User/getuserinfo')->middleware(['ApiGetUserid']);
    // 统计用户数据
    Route::get('user/getcounts/:user_id','api/:version.User/getCounts');
    // 微信小程序登录

    // 支付宝小程序登录

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
    // 判断当前用户第三方登录绑定情况
    Route::get('user/getuserbind','api/:version.User/getUserBind');
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
    //用户评论
    Route::post('post/comment','api/:version.Comment/comment');
    //修改头像
    Route::post('edituserpic','api/:version.User/editUserpic');
    //修改资料
    Route::post('edituserinfo','api/:version.User/editUserinfo');
    //修改密码
    Route::post('repassword','api/:version.User/rePassword');
    //添加黑名单    
    Route::post('addblack','api/:version.Blacklist/addBlack');
    //移除黑名单    
    Route::post('removeblack','api/:version.Blacklist/removeBlack');
    //关注
    Route::post('follow','api/:version.User/follow');
    //取消关注
    Route::post('unfollow','api/:version.User/unfollow');
    //互关列表
    Route::get('friends/:page','api/:version.User/friends');
    //粉丝列表
    Route::get('fens/:page','api/:version.User/fens');
    //关注列表
    Route::get('follows/:page','api/:version.User/follows');
    //用户反馈
    Route::post('feedback','api/:version.Feedback/feedback');
    //获取用户反馈列表
    Route::get('feedbacklist/:page','api/:version.Feedback/feedbacklist');
})->middleware(['ApiUserAuth','ApiUserBindPhone','ApiUserStatus']);


//socket
Route::group('api/:version/',function (){
    //发送消息
    Route::post('chat/send','api/:version.Chat/send');
    //接收未接收消息
    Route::post('chat/get','api/:version.Chat/get');
    //绑定上线
    Route::post('chat/bind','api/:version.Chat/bind');
})->middleware(['ApiUserAuth','ApiUserBindPhone','ApiUserStatus']);

