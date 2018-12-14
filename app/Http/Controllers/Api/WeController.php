<?php

namespace App\Http\Controllers\Api;

use Request;
use EasyWeChat\Factory;

class WeController extends Controller
{
    public function wxconfig()
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        return $app->jssdk->buildConfig(array('onMenuShareQQ', 'onMenuShareWeibo'), true) ;
    }
}
