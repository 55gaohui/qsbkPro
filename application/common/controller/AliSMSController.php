<?php

namespace app\common\controller;
// 引入阿里sdk
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;


class AliSMSController
{
    static public function SendSMS($phone, $code)
    {
        AlibabaCloud::accessKeyClient(config('api.aliSMS.accessKeyId'), config('api.aliSMS.accessSecret'))->regionId(config('api.aliSMS.regionId'))->asGlobalClient();
        try {
            $option = [
                'query' => [
                    'RegionId' => config('api.aliSMS.regionId'),
                    'PhoneNumbers' => $phone,
                    'SignName' => config('api.aliSMS.SignName'),
                    'TemplateCode' => config('api.aliSMS.TemplateCode'),
                    'TemplateParam' => '{"code":"' . $code . '"}',
                ],
            ];
            $result = AlibabaCloud::rpcRequest()
                ->product(config('api.aliSMS.product'))
                // ->scheme('https') // https | http
                ->version(config('api.aliSMS.version'))
                ->action('SendSms')
                ->method('GET')
                ->options($option)->request();
            return $result->toArray();
        } catch (ClientException $e) {
            TApiException(200,$e->getErrorMessage(),30000);
        } catch (ServerException $e) {
            TApiException(200,$e->getErrorMessage(),30000);
        }
    }
}
