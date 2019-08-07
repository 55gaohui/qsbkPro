<?php

namespace app\common\model;

use think\Model;

class Image extends Model
{
    // 自动写入时间
    protected $autoWriteTimestamp = true;
    //上传多图
    public function uploadMore()
    {
        $image = $this->upload(request()->userId, 'imglist');
        for($i=0; $i<count($image); $i++){
            $image[$i]['url'] = getFileUrl($image[$i]['url']);
        }
        return $image;
    }
    // 上传图片
    public function upload($userid = '',$field = '')
    {
        $files = request()->file($field);
        //单图上传
        if(!$files) TApiException(200,'请上传图片',10000);
        if(is_array($files)){
            // 多图上传
            $arr = [];
            foreach($files as $file){
                $res = \app\common\controller\FileController::UploadEvent($file);
                if($res['status']){
                    $arr[] =[
                        'url'=> $res['data'],
                        'user_id'=> $userid
                    ];
                }
            }
            return $this->saveAll($arr);
        }
        //单文件上传
        $file = \app\common\controller\FileController::UploadEvent($files);
        //上传失败
        if(!$file['status']) TApiException(200,$file['data'],10000);
        //上传成功，写入数据库
        return self::create([
            'url'=>$file['data'],
            'user_id'=>$userid
        ]);
    }
    //    验证图片是否是否为当前用户上传
    public function isImageExist($id,$user_id){
        return $this->where('user_id',$user_id)->find($id);
    }
}
