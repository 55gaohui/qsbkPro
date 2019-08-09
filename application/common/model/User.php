<?php

namespace app\common\model;

use think\Db;
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
            if($user) $user->logintype = 'phone';
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
            if($user) $user->logintype = 'email';
            return $user;
        }
        // 用户名
        if(array_key_exists('username',$arr)){
            $user = $this->where('username',$arr['username'])->find();
            if($user) $user->logintype = 'username';
            return $user;
        }
        //第三方登录
        if(array_key_exists('provider',$arr)){
            $where = [
                'type'=> $arr['provider'],
                'openid' => $arr['openid']
            ];
            $user = $this->userbind()->where($where)->find();
            if($user) $user->logintype = $arr['provider'];
            return $user;
        }
        return false;
    }
    //绑定用户信息表
    public function userinfo(){
        return $this->hasOne('Userinfo');
    }
    //绑定第三方表
    public function userbind(){
        return $this->hasMany('UserBind');
    }
    //绑定文章表
    public function post(){
        return $this->hasMany('Post');
    }
    //绑定粉丝表
    public function withfollow(){
        return $this->hasMany('Follow','user_id');
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
            $user->logintype = 'phone';
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

    //绑定手机
    public function bindphone(){
        //获取所有参数
        $params = request()->param();
        $currentUserInfo = request()->userTokenUserInfo;
        $currentUserId = request()->userId;
        //当前登录类型
        $currentLoginType = $currentUserInfo['logintype'];
        // 验证绑定类型是否冲突
        $this->checkBindType($currentLoginType,'phone');
        //查询该手机是否被注册
        $binduser = $this->isExist(['phone'=>$params['phone']]);
        //已存在
        if($binduser){
            // 账号邮箱登录
            if($currentLoginType == 'username' || $currentLoginType == 'email') TApiException(200,'手机号已被绑定',20006);
            //第三方登录
            if($binduser->userbind()->where('type',$currentLoginType)->find()) TApiException(200,'手机号已被绑定',20006);
            //直接修改
            $userbind = $this->userbind()->find($currentUserInfo['id']);
            $userbind->user_id = $binduser->id;
            if($userbind->save()){
                //更新缓存
                $currentUserInfo['user_id'] = $binduser->id;
                Cache::set($currentUserInfo['token'],$currentUserInfo,$currentUserInfo['expires_in']);
                return true;
            }
            TApiException('手机号绑定失败');
        }
        //不存在
        //账号邮箱登录
        if($currentLoginType == 'username' || $currentLoginType == 'email'){
            $user =  $this->save([
                'phone'=>$params['phone']
            ],['id'=>$currentUserId]);
            //更新缓存
            $currentUserInfo['phone'] = $params['phone'];
            Cache::set($currentUserInfo['token'],$currentUserInfo,config('api.token_expire'));
            return true;
        }
        //第三方登录（user_id=0）
        if(!$currentUserId){
            // 在user表创建账号
            $user = $this->create([
                'username'=>$params['phone'],
                'phone'=>$params['phone']
            ]);
            //绑定
            $userbind = $this->userbind()->find($currentUserInfo['id']);
            $userbind->user_id = $user->id;
            if($userbind->save()){
                //更新缓存
                $currentUserInfo['user_id'] = $user->id;
                Cache::set($currentUserInfo['token'],$currentUserInfo,$currentUserInfo['expires_in']);
                return true;
            }
            TApiException('手机号绑定失败');
        }
        //直接修改(user_id != 0)
        if($this->save([
            'phone'=>$params['phone']
        ],['id'=>$currentUserId])) return true;
        TApiException('手机号绑定失败');
    }
    //绑定邮箱
    public function bindemail(){
        //获取所有参数
        $params = request()->param();
        $currentUserInfo = request()->userTokenUserInfo;
        $currentUserId = request()->userId;
        //当前登录类型
        $currentLoginType = $currentUserInfo['logintype'];
        // 验证绑定类型是否冲突
        $this->checkBindType($currentLoginType,'email');
        //查询邮箱是否存在
        $binduser = $this->isExist(['email'=>$params['email']]);
        //邮箱存在
        if($binduser){
            //账号手机号登录
            if($currentLoginType == 'username' || $currentLoginType == 'phone') TApiException(200,'邮箱已被绑定',20006);
            //第三方登录 邮箱账号绑定了当前第三方登录类型
            if($binduser->userbind()->where('type',$currentLoginType)->find()) TApiException(200,'邮箱已被绑定',20006);
            // 邮箱账号未绑定当前第三方登录类型  直接修改
            $userbind = $this->userbind()->find($currentUserInfo['id']);
            $userbind->user_id = $binduser->id;
            if($userbind->save()){
                //更新缓存
                $currentUserInfo['user_id'] = $binduser->id;
                Cache::set($currentUserInfo['token'],$currentUserInfo,$currentUserInfo['expires_in']);
                return true;
            }
            TApiException('邮箱绑定失败');
        }
        //邮箱未绑定 不存在
        //账号手机号登录
        if($currentLoginType == 'username' || $currentLoginType == 'phone'){
            $user = $this->save([
                'email'=>$params['email']
            ],['id'=>$currentUserInfo['id']]);
            // 更新缓存
            $currentUserInfo['email'] = $params['email'];
            Cache::set($currentUserInfo['token'],$currentUserInfo,config('api.token_expire'));
            return true;
        }
        //第三方登录
        if(!$currentUserId){
            // 在user表创建账号
            $user = $this->create([
                'username'=>$params['email'],
                'email'=>$params['email']
            ]);
            //绑定
            $userbind = $this->userbind()->find($currentUserInfo['id']);
            $userbind->user_id = $user->id;
            if($userbind->save()){
                //更新缓存
                $currentUserInfo['user_id'] = $user->id;
                Cache::set($currentUserInfo['token'],$currentUserInfo,$currentUserInfo['expires_in']);
                return true;
            }
            TApiException('邮箱绑定失败');
        }
        //直接修改
        if($this->save([
            'email'=>$params['email']
        ],['id'=>$currentUserId])) return true;
        TApiException('邮箱绑定失败');




    }
    //绑定第三方
    public function bindother(){
        // 获取所有参数
        $params = request()->param();
        $currentUserInfo = request()->userTokenUserInfo;
        $currentUserId = request()->userId;
        //当前登录类型
        $currentLoginType = $currentUserInfo['logintype'];
        //验证绑定类型是否冲突
        $this->checkBindType($currentLoginType,$params['provider']);
        //查询该第三方是否存在
        $binduser = $this->isExist(['provider'=>$params['provider'],'openid'=>$params['openid']]);
        //第三方信息存在
        if($binduser){
            //第三方的user_id>0  (已绑定)
            if($binduser->user_id) TApiException(200,$params['provider'].'已被绑定',20006);
            //第三方的user_id=0 （未绑定）
            //绑定
            $binduser->user_id = $currentUserInfo['id'];
            return $binduser->save();
        }
        //不存在
        return $this->userbind()->create([
            'type'=>$params['provider'],
            'openid'=>$params['openid'],
            'nickname'=>$params['nickName'],
            'avatarurl'=>$params['avatarUrl'],
            'user_id'=>$currentUserId
        ]);
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
    //验证当前绑定类型是否冲突
    public function checkBindType($current,$bindtype){
        //当前绑定类型
        if($bindtype == $current) TApiException('绑定类型冲突');
        return true;
    }
    //修改用户头像
    public function editUserpic(){
        //获取所有参数
        $params = request()->param();
        //获取用户ID
        $userid = request()->userId;
        $image = (new Image())->upload($userid,'userpic');
        //修改用户头像
        $user = self::get($userid);
        $user->userpic = getFileUrl($image->url);  //getFileUrl添加完整地址
        if($user->save()) return true;
        TApiException();
    }
    //修改用户资料
    public function editUserinfo(){
        //获取所有参数
        $params = request()->param();
        //获取用户ID
        $userid = request()->userId;

        $user = self::get($userid);
        $user->username = $params['name'];
        $user->save();
        $userinfo = $user->userinfo()->find();
        $userinfo->sex = $params['sex'];
        $userinfo->qg = $params['qg'];
        $userinfo->job = $params['job'];
        $userinfo->birthday = $params['birthday'];
        $userinfo->path = $params['path'];
        $userinfo->save();
        return true;
    }
    //修改密码
    public function repassword(){
        //获取所有参数
        $params = request()->param();
        //获取用户ID
        $userid = request()->userId;

        $user = self::get($userid);
        // 手机注册的用户并没有原密码,直接修改即可
        if($user['password']){
            //检验老密码是否有误
            $this->checkPassword($params['oldpassword'],$user['password']);
        }
        //修改密码
        $newpassword = password_hash($params['newpassword'],PASSWORD_DEFAULT);
        $res = $user->save([
            'password'=>$newpassword
        ],['id'=>$userid]);
        if(!$res) TApiException(200,'修改密码失败',20009);
        $user['password'] = $newpassword;
        // 更新缓存信息
        Cache::set(request()->Token,$user,config('api.token_expire'));
    }
    //关注
    public function ToFollow(){
        // 获取所有参数
        $params = request()->param();
        // 获取用户id
        $userid = request()->userId;
        $follow_id = $params['follow_id'];
        if($follow_id == $userid) TApiException(200,'非法操作',10000);
        // 获取到当前用户的关注模型
        $followModel  = $this->get($userid)->withfollow();
        //查询记录是否存在
        $follow = $followModel->where('follow_id',$follow_id)->find();
        if($follow) TApiException(200,'已经关注过了',10000);
        $followModel->create([
            'user_id'=>$userid,
            'follow_id'=>$follow_id
        ]);
        return true;
    }
    //取消关注
    public function ToUnFollow(){
        // 获取所有参数
        $params = request()->param();
        // 获取用户id
        $userid = request()->userId;
        $follow_id = $params['follow_id'];
        if($follow_id == $userid) TApiException(200,'非法操作',10000);
        // 获取到当前用户的关注模型
        $followModel  = $this->get($userid)->withfollow();
        //查询记录是否存在
        $follow = $followModel->where('follow_id',$follow_id)->find();
        if(!$follow) TApiException(200,'暂未关注',10000);
        $follow->delete();
        return true;
    }
    //互关列表
    public function getFriendsList(){
        // 获取所有参数
        $params = request()->param();
        // 获取用户id
        $userid = request()->userId;
        $page = $params['page'];
        $follows = Db::table('user')->where('id', 'IN', function($query) use($userid){
          $query->table('follow')->where('user_id', 'IN', function($query) use($userid){
              $query->table('follow')->where('user_id',$userid)->field('follow_id');
            })->where('follow_id',$userid)->field('user_id');
        })->field('id,username,userpic')->select();
        return $follows;
    }
    // 关联粉丝列表
    public function fens(){
        return $this->belongsToMany('User','Follow','user_id','follow_id');
    }
    //粉丝列表
    public function getFensList(){
        // 获取所有参数
        $params = request()->param();
        // 获取用户id
        $userid = request()->userId;

        $fens = $this->get($userid)->fens()->page($params['page'],10)->select()->toArray();
        return $this->filterReturn($fens);
    }
    // 关联关注列表
    public function follows(){
        return $this->belongsToMany('User','Follow','follow_id','user_id');
    }
    //关注列表
    public function getFollowsList(){
        // 获取所有参数
        $params = request()->param();
        // 获取用户id
        $userid = request()->userId;
        $follows = $this->get($userid)->follows()->page($params['page'],10)->select()->toArray();
        return $this->filterReturn($follows);
    }

    //粉丝列表和关注列表过滤字段
    public function filterReturn($params = []){
        $arr = [];
        $length = count($params);
        for($i= 0; $i<$length; $i++){
            $arr[] = [
                'id'=>$params[$i]['id'],
                'username'=>$params[$i]['username'],
                'userpic'=>$params[$i]['userpic']
            ];
        }
        return $arr;
    }
}
