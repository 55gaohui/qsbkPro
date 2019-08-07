<?php

namespace app\common\controller;

use think\Request;
// 上传单文件
class FileController
{
    // 上传单文件
    static public function UploadEvent($files,$size=2067800,$ext='jpg,png,gif',$path='uploads')
    {
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $files->validate(['size'=>$size,'ext'=>$ext])->move($path);
        return [
            'data'=> $info ? $info->getPathname() : $files->getError(),
            'status'=> $info ? true :false
        ];
    }

    
}
