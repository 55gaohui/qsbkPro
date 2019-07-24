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
        'otherlogin' => ['provider','openid','nickName','avatarUrl','expires_in']
    ];
}
