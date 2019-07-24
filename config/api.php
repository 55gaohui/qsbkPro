<?php
// +----------------------------------------------------------------------
// | 阿里大于设置
// +----------------------------------------------------------------------
return [
    // token过期时间
    'token_expire'=>0,
    // 阿里大于
    'aliSMS' => [
        'isopen' => false,//开启阿里大于
        'accessKeyId' => 'LTAIOnToUr2JXoED',
        'accessSecret' => 'K1xdc1QO4eb1Hmiajh1BdQvwQZhVGH',
        'regionId' => 'cn-hangzhou',
        'product' => 'Dysmsapi',
        'version' => '2017-05-25',
        'SignName' => '测试仿糗事',
        'TemplateCode' => 'SMS_170836115',
        // 验证码有效期
        'expire' => 60
    ],
];