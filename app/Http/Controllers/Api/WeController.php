<?php

namespace App\Http\Controllers\Api;

use Request;
use EasyWeChat\Factory;

class WeController extends Controller
{
    public function wxconfig()
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $jsApiList = ['onMenuShareQQ', 'onMenuShareWeibo'];
        return $app->jssdk->buildConfig($jsApiList, false) ;
    }
    public function wxauth()
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $oauth = $app->oauth;
        return $oauth->redirect();
    }
}
