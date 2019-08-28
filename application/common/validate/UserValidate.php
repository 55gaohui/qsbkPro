<?php

namespace app\common\validate;

class UserValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'phone' => 'require|mobile',
        'code'=>'require|number|length:4|isPefectCode',
        'username' => 'require',
        'password' => 'require|alphaDash',
        'provider'=>'require',
        'openid'=>'require',
        'nickName'=>'require',
        'avatarUrl'=>'require',
        'expires_in'=>'require',
        'id'=>'require|integer|>:0',
        'page'=>'require|integer|>:0',
        'userpic'=>'image',
        'name'=>'require|chsDash',
        'sex'=>'require|in:0,1,2',
        'qg'=>'require|in:0,1,2',
        'job'=>'require|chsAlpha',
        'birthday'=>'require|dateFormat:Y-m-d',
        'path'=>'require|chsDash',
        'oldpassword'=>'require',
        'newpassword'=>'require|alphaDash',
        'renewpassword'=>'require|confirm:newpassword',
        'follow_id'=>'require|integer|>:0|isUserExist',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'phone.require' => '请输入手机号',
        'phone.mobile' => '手机号格式有误',
        'code.require' => '请输入验证码',
        'code.number' => '验证码必须为数字',
        'code.length' => '验证码为四位',
        'username.require' => '请输入用户名',
        'password.require' => '请输入密码',
        'password.alphaDash' => '密码输入有误',
    ];
    /**
     * 定义验证场景
     * @var array
     */
    protected $scene = [
        // 发送验证码
        'sendCode'=>['phone'],
        // 手机号登录
        'phonelogin'=>['phone','code'],
        //用户名邮箱手机号登录
        'login'=>['username','password'],
        //第三方登录
        'otherlogin' => ['provider','openid','nickName','avatarUrl','expires_in'],
        //指定用户发布的文章列表
        'post'=>['id','page'],
        //当前用户发布的文章列表
        'allpost'=>['page'],
        //绑定手机
        'bindphone'=>['phone'],
        //绑定邮箱
        'bindemail'=>['email'],
        //绑定第三方
        'bindother'=>['provider','openid','nickName','avatarUrl'],
        //修改头像
        'edituserpic'=>['userpic'],
        //修改资料
        'edituserinfo'=>['name','sex','qg','job','birthday','path'],
        //修改密码
        'repassword'=>['oldpassword','newpassword','renewpassword'],
        //关注
        'follow'=>['follow_id'],
        //互关
        'getfriends'=>['page'],
        //粉丝列表
        'getfens'=>['page'],
        //关注列表
        'getfollows'=>['page'],
        //获取指定用户详细信息
        'getuserinfo'=>['user_id'],
    ];
}
