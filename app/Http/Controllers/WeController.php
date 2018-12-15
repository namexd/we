<?php

namespace App\Http\Controllers;

use Request;
use EasyWeChat\Factory;

class WeController extends Controller
{
    public function oauth()
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $oauth = $app->oauth;
        return $oauth->redirect();
    }

    public function callback()
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();

        $userInfo = $user->toArray();
        session('userInfo',$userInfo);
        return redirect('/ucenter');

    }
    
}
