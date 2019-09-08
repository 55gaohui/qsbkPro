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
        $user = (new UserModel())->phoneLogin();
        return self::showResCode('登录成功',$user);
    }
    //用户名邮箱手机号登录
    public function login(){
        // 验证登录信息
        (new UserValidate())->goCheck('login');
        $user = (new UserModel())->login();
        return self::showResCode('登录成功',$user);
    }
    //第三方登录
    public function otherLogin(){
        // 验证登录信息
        (new UserValidate())->goCheck('otherlogin');
        $user = (new UserModel())->otherLogin();
        return self::showResCode('登录成功',$user);
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

    //绑定手机
    public function bindphone()
    {
        (new UserValidate())->goCheck('bindphone');
        $user = (new UserModel())->bindphone();
        return self::showResCode('绑定成功',$user);
    }
    //绑定邮箱
    public function bindemail()
    {
        (new UserValidate())->goCheck('bindemail');
        (new UserModel())->bindemail();
        return self::showResCode('绑定成功');
    }
    //绑定第三方
    public function bindother()
    {
        (new UserValidate())->goCheck('bindother');
        (new UserModel())->bindother();
        return self::showResCode('绑定成功');
    }

    //修改头像
    public function editUserpic()
    {
        (new UserValidate())->goCheck('edituserpic');
        $src = (new UserModel())->editUserpic();
        return self::showResCode('修改成功',$src);
    }

    //修改用户资料
    public function editUserinfo()
    {
        (new UserValidate())->goCheck('edituserinfo');
        (new UserModel())->editUserinfo();
        return self::showResCode('修改成功');
    }

    //修改密码
    public function rePassword()
    {
        (new UserValidate())->goCheck('repassword');
        (new UserModel())->repassword();
        return self::showResCode('修改成功');
    }

    //关注
    public function follow(){
        (new UserValidate())->goCheck('follow');
        (new UserModel())->ToFollow();
        return self::showResCode('关注成功');
    }
    //取消关注
    public function unfollow(){
        (new UserValidate())->goCheck('follow');
        (new UserModel())->ToUnFollow();
        return self::showResCode('取消关注成功');
    }
    //互关列表
    public function friends(){
        (new UserValidate())->goCheck('getfriends');
        $list = (new UserModel())->getFriendsList();
        return self::showResCode('获取成功',['list'=>$list]);
    }
    //粉丝列表
    public function fens(){
        (new UserValidate())->goCheck('getfens');
        $list = (new UserModel())->getFensList();
        return self::showResCode('获取成功',['list'=>$list]);
    }
    //关注列表
    public function follows(){
        (new UserValidate())->goCheck('getfollows');
        $list = (new UserModel())->getFollowsList();
        return self::showResCode('获取成功',['list'=>$list]);
    }
    //获取用户数据
    public function getCounts(){
        (new UserValidate())->goCheck('getuserinfo');
        $user = (new UserModel())->getCounts();
        return self::showResCode('获取成功',$user);
    }
    //获取指定用户详细信息
    public function getuserinfo(){
        (new UserValidate())->goCheck('getuserinfo');
        $data = (new UserModel())->getUserInfo();
        return self::showResCode('获取成功',['data'=>$data]);
    }
    //判断当前用户userid的第三方登录绑定情况
    public function getUserBind(){
        $user = (new UserModel())->getUserBind();
        return self::showResCode('获取成功',$user);
    }
}