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

// +----------------------------------------------------------------------
// | 中间件配置
// +----------------------------------------------------------------------
return [
    // 默认中间件命名空间
    'ApiUserAuth' => app\http\middleware\ApiUserAuth::class,
    'ApiUserBindPhone' => app\http\middleware\ApiUserBindPhone::class,
    'ApiUserStatus' => app\http\middleware\ApiUserStatus::class,
    'ApiGetUserid' => app\http\middleware\ApiGetUserid::class,
];
