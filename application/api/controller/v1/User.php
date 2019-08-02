<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\validate\UserValidate;
use app\common\model\User as UserModel;

class User extends BaseController
{
    //发送验证码
    public function sendCode(){
        (new UserValidate())->goCheck('sendCode');
        (new UserModel())->sendCode();
        return self::showResCodeWithOutData('发送成功');
    }
    //手机号登录注册
    public function phoneLogin(){
        // 验证登录信息
        (new UserValidate())->goCheck('phonelogin');
        $token = (new UserModel())->phoneLogin();
        return self::showResCode('登录成功',['token'=>$token]);
    }
    //用户名邮箱手机号登录
    public function login(){
        // 验证登录信息
        (new UserValidate())->goCheck('login');
        $token = (new UserModel())->login();
        return self::showResCode('登录成功',['token'=>$token]);
    }
    //第三方登录
    public function otherLogin(){
        // 验证登录信息
        (new UserValidate())->goCheck('otherlogin');
        $token = (new UserModel())->otherLogin();
        return self::showResCode('登录成功',['token'=>$token]);
    }
    //退出登录
    public function logout()
    {
        (new UserModel())->logout();
        return "退出登录";
    }
    //指定用户发布的文章列表
    public function post(){
        (new UserValidate())->goCheck('post');
        $list = (new UserModel())->getPostList();
        return self::showResCode('获取成功',['list'=>$list]);
    }
    //当前用户发布的文章列表（含隐私）
    public function Allpost(){
        (new UserValidate())->goCheck('allpost');
        $list = (new UserModel())->getAllPostList();
        return self::showResCode('获取成功',['list'=>$list]);
    }
}