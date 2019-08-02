<?php

namespace app\common\model;

use think\facade\Cache;
use think\Model;
class User extends Model
{
    //自动写入时间
    protected $autoWriteTimestamp = true;
    //发送验证码
    public function sendCode(){
        // 获取用户提交手机号码
        $phone = request()->param('phone');
        // 判断是否已经发送过
        if(Cache::get($phone)) TApiException(200,'你操作得太快了',30001);
        // 生成4位验证码
        $code = random_int(1000,9999);
        // 判断是否开启验证码功能
        if(!config('api.aliSMS.isopen')){
            Cache::set($phone,$code,config('api.aliSMS.expire'));
            TApiException(200,'验证码：'.$code,30005);
        }
        // 发送验证码
        $res = AliSMSController::SendSMS($phone,$code);
        //发送成功 写入缓存
        if($res['Code']=='OK') return Cache::set($phone,$code,config('api.aliSMS.expire'));
        // 无效号码
        if($res['Code']=='isv.MOBILE_NUMBER_ILLEGAL') TApiException(200,'无效号码',30002);
        // 触发日限制
        if($res['Code']=='isv.DAY_LIMIT_CONTROL') TApiException(200,'今日你已经发送超过限制，改日再来',30002);
        // 发送失败
        TApiException(200,'发送失败',30004);
    }
    // 判断用户是否存在
    public function isExist($arr = []){
        if(!is_array($arr)) return false;
        // 手机号码
        if(array_key_exists('phone',$arr)){
            $user = $this->where('phone',$arr['phone'])->find();
            return $user;
        };
        // 用户id
        if(array_key_exists('id',$arr)){
            $user = $this->where('id',$arr['id'])->find();
            return $user;
        }
        // 邮箱
        if(array_key_exists('email',$arr)){
            $user = $this->where('email',$arr['email'])->find();
            return $user;
        }
        // 用户名
        if(array_key_exists('username',$arr)){
            $user = $this->where('username',$arr['username'])->find();
            return $user;
        }
        //第三方登录
        if(array_key_exists('provider',$arr)){
            $where = [
                'type'=> $arr['provider'],
                'openid' => $arr['openid']
            ];
            $user = $this->userbind()->where($where)->find();
            return $user;
        }
        return false;
    }
    //绑定用户信息表
    public function userinfo(){
        return $this->hasOne('Userinfo');
    }
    //绑定用户信息表
    public function userbind(){
        return $this->hasMany('UserBind');
    }
    //绑定用户信息表
    public function post(){
        return $this->hasMany('Post');
    }
    //验证手机登录
    public function phoneLogin(){
        // 获取用户提交手机号码及验证码
        $param = request()->param();
        // 验证用户是否存在
        $user = $this->isExist(['phone'=>$param['phone']]);
        // 用户不存在，直接注册
        if(!$user){
            // 用户主表
            $user = self::create([
                'username'=> $param['phone'],
                'phone' => $param['phone'],
                 'password'=>password_hash($param['phone'],PASSWORD_DEFAULT)
            ]);
            // 在用户信息表创建对应的记录（用户存放用户其他信息）
            $user->userinfo()->create(['user_id'=> $user->id]);
            return $this->CreateSaveToken($user->toArray());
        }
        // 用户是否被禁用
        $this->checkStatus($user->toArray());
        // 登录成功，返回token和用户信息
        $userarr = $user->toArray();
        return $this->CreateSaveToken($userarr);
    }
    // 账户登录
    public function login(){
        // 获取用户提交用户名及密码
        $param = request()->param();
        // 验证用户是否存在
        $user = $this->isExist($this->filterUserData($param['username']));
        //用户不存在
        if(!$user) TApiException(200,'昵称/邮箱/手机号有误',20000);
        //用户是否被禁用
        $this->checkStatus($user->toArray());
        //验证密码
        $this->checkPassword($param['password'], $user->password);
        //登录成功 生成token 进行缓存 返回客户端
        $userarr = $user->toArray();
        return $this->CreateSaveToken($userarr);

    }
    //第三方登录
    public function otherLogin()
    {
        // 获取用户信息
        $param = request()->param();
        // 解密过程（待添加）
        // 验证用户是否存在
        $user = $this->isExist(['provider'=>$param['provider'],'openid'=>$param['openid']]);
        // 用户不存在，创建用户
        $arr = [];
        if(!$user){
            $user = $this->userbind()->create([
                'type'=>$param['provider'],
                'openid'=>$param['openid'],
                'nickname'=>$param['nickName'],
                'avatarurl'=>$param['avatarUrl'],
            ]);
            $arr = $user->toArray();
            $arr['user_id'] = 0;
            $arr['expires_in'] = $param['expires_in'];
            $arr['logintype'] = $param['provider'];
            $arr['token'] = $this->CreateSaveToken($arr);
            return $arr;
        }
        // 用户是否被禁用
        $arr = $this->checkStatus($user->toArray(), true);
        // 登录成功，返回用户信息+token
        $arr['expires_in'] = $param['expires_in'];
        $userarr = $user->toArray();
        $userarr['token'] = $this->CreateSaveToken($arr);
        return $userarr;
    }
    //退出登录
    public function logout(){
        // 获取并清除缓存
        if(!Cache::pull(request()->userToken)) TApiException(200,'您已经退出了',30006);
        return true;
    }

    //指定用户发布的文章列表
    public function getPostList(){
        $params = request()->param();
        $list = self::get($params['id'])->post()->with(['user'=>function($query){
            return $query->field('id,username,userpic');
        },'images'=>function ($query){
            return $query->field('url');
        },'share'])->page($params['page'],10)->select();
        return $list;
    }
    //当前用户发布的文章列表
    public function getAllPostList(){
        $params = request()->param();
        $user_id = request()->userId;
        $list = self::get($user_id)->post()->with(['user'=>function($query){
            return $query->field('id,username,userpic');
        },'images'=>function($query){
            return $query->field('url');
        },'share'])->page($params['page'],10)->select();
        return $list;
    }

    //搜索用户
    public function search(){
        $params = request()->param();
        $list = $this->whereLike('username','%'.$params['keyword'].'%')->page($params['page'],10)->select();
        return $list;
    }

    //验证用户是否被禁用
    public function checkStatus($arr, $isReget = false)
    {
        $status = 1;
        if($isReget){
            //  账号密码登录 和 第三方登录
            $userid = array_key_exists('user_id', $arr) ? $arr['user_id'] : $arr['id'];
            // 判断第三方登录是否绑定了手机号码
            if($userid < 1) return $arr;
            // 查询user表
            $user = $this->find($userid)->toArray();
            //拿到status
            $status = $user['status'];
        }else{
            $status = $arr['status'];
        }
        if($status == 0) TApiException(200,'该用户已被禁用',20001);
        return $arr;

    }
    // 验证用户名是什么格式，昵称/邮箱/手机号
    public function filterUserData($data)
    {
        $arr = [];
        // 验证是否是手机号码
        if(preg_match('^1(3|4|5|7|8)[0-9]\d{8}$^',$data)){
            $arr['phone'] = $data;
            return $arr;
        }
        // 验证是否是邮箱
        if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',$data)){
            $arr['email'] = $data;
            return $arr;
        }
        $arr['username'] = $data;
        return $arr;
    }
    //验证密码是否正确
    public function checkPassword($password,$hash)
    {
        if(!$hash) TApiException(200,'密码错误',20002);
        // 密码错误
        if(!password_verify($password,$hash)) TApiException(200,'密码错误',20002);
    }
    // 生成并保存token
    public function CreateSaveToken($arr = []){
        // 生成token
        $token = sha1(md5(uniqid(md5(microtime(true)),true)));
        $arr['token'] = $token;
        // 登录过期时间
        $expire =array_key_exists('expires_in',$arr) ? $arr['expires_in'] : config('api.token_expire');
        // 保存到缓存中
        if(!Cache::set($token,$arr,$expire)) TApiException();   //1：名称，2：值，3：缓存时间
        // 返回token
        return $token;
    }

}
