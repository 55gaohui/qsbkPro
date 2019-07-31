<?php


namespace app\common\validate;
use think\Validate;
use app\lib\exception\BaseException;

class BaseValidate extends Validate
{
    public function goCheck($scene = false){
        // 获取请求传递过来的所有参数
        $params = request()->param();
        //开始验证
        $check = $scene ? $this->scene($scene)->check($params) : $this->check($params);
        //开始验证
        if(!$check){
            throw new BaseException(['msg'=>$this->getError(),'errorCode'=>10000,'code'=>400]);
        }
    }

    /**
     * 验证码验证
     * @param $value 用户输入的值
     * @param string $rule 验证规则
     * @param string $data 用户当前场景的值 全部数据（数组）    'phoneLogin' => ['phone','code']
     * @param string $field   当前字段名 code
     */
    protected function isPefectCode($value, $rule='', $data='', $field=''){
        //验证码不存在
        $beforeCode = cache($data['phone']);
        if(!$beforeCode) return "请重新获取验证码";
        // 验证验证码
        if($value != $beforeCode) return "验证码错误";
        return true;
    }

    /**
     * @param $value 话题id
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool
     */
    protected function isTopicExist($value, $rule='', $data='', $field=''){
        if($value == 0) return true;
        if(\app\common\model\Topic::field('id')->find($value)) return true;
        return '该话题不存在';
    }
//    文章分类是否存在
    protected function isPostClassExist($value, $rule='', $data='', $field=''){
        if(\app\common\model\PostClass::field('id')->find($value)) return true;
        return '该文章分类不存在';
    }
}