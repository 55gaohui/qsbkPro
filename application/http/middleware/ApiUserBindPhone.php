<?php

namespace app\http\middleware;

class ApiUserBindPhone
{
    public function handle($request, \Closure $next)
    {
        if($request->userId < 1) TApiException(200,'请先绑定手机！',20008);
        return $next($request);
    }
}
