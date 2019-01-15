<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WeappHasWeuser;
use App\Models\Weuser;
use EasyWeChat\OfficialAccount\Application;
use Redirect;
use Request;
use EasyWeChat\Factory;

/**
 * 智慧冷链公众号：网页版微信授权
 * Class WeController
 * @package App\Http\Controllers
 */
class WeController extends Controller
{
    private $weapp_id = 1;
    private $﻿redirect_url = '/ucenter/#/';

    public function test()
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $list = $app->customer_service->list();
        dd($list);
    }

    public function oauth()
    {
        $scopes = 'snsapi_base';//'snsapi_userinfo';//
        if (request('scopes')) {
            $scopes = request('scopes');
        }
        if (request('redirect_url')) {
            request()->session()->put('callback_url', urldecode(request('redirect_url')));
        } else {
            request()->session('callback_url', $this->﻿redirect_url);
        }
        $app = Factory::officialAccount(config('wechat.official_account.default'));

        $oauth = $app->oauth->scopes([$scopes]);
//        $oauth = $app->oauth;
        return $oauth->redirect();
    }

    public function callback()
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $wxuser = $oauth->user();
        $userInfo = $wxuser->toArray();

        // 检测we.openid 是否存
        $hasWeuser = WeappHasWeuser::where('weapp_id', $this->weapp_id)->where('openid', $userInfo['original']['openid'])->first();
        //如果简单版本的snsapi_base获取到的openid不存着数据库中，需要再次请求获取用户全部资料
        if (!$hasWeuser and !isset($userInfo['original']['unionid'])) {
            return redirect('/we/oauth?scopes=snsapi_userinfo');
        }
        if (!$hasWeuser) {
            // 改用检测we.unionid 是否存
            $hasWeuser = WeappHasWeuser::where('unionid', $userInfo['original']['unionid'])->first();
            // 有unionid 但是没有openid，则为小程序等第二应用的用户，插入关系
            if ($hasWeuser) {
                $new_weappHasWeuser = [
                    'weapp_id' => $this->weapp_id,
                    'weuser_id' => $hasWeuser->weuser_id,
                    'openid' => $userInfo['original']['openid'],
                    'unionid' => $userInfo['original']['unionid'],
                ];
                $new_weappHasWeuser = new WeappHasWeuser($new_weappHasWeuser);
                $new_weappHasWeuser->save();
            }
        }
        if ($hasWeuser) {
            // 如果openid存在，检测unionid字段是否为空，为空则更新
            if (!$hasWeuser->unionid and $userInfo['original']['unionid']) {
                $hasWeuser->unionid = $userInfo['original']['unionid'];
                $hasWeuser->save();
            }
        } else {
            // 如果openid不存在，创建user,
            $new_user = [
                'name' => $userInfo['original']['nickname']
            ];
            $user = new User($new_user);
            $user->save();

            $new_weuser = [
                'nickname' => $userInfo['original']['nickname'],
                'sex' => $userInfo['original']['sex'],
                'language' => $userInfo['original']['language'],
                'city' => $userInfo['original']['city'],
                'province' => $userInfo['original']['province'],
                'country' => $userInfo['original']['country'],
                'headimgurl' => $userInfo['original']['headimgurl'],
                'privilege' => json_encode($userInfo['original']['privilege'])
            ];
            $weuser = new Weuser($new_weuser);
            $user->weuser()->save($weuser);
            $new_weappHasWeuser = [
                'weapp_id' => $this->weapp_id,
                'openid' => $userInfo['original']['openid'],
                'unionid' => $userInfo['original']['unionid'],
                'weuser_id' => $user->weuser->id,
            ];
            $weappHasWeuser = new WeappHasWeuser($new_weappHasWeuser);
            $weappHasWeuser->save();
//            $user->weappHasWeuser()->save($weappHasWeuser);
        }
        $access_token = $userInfo['original']['access_token'] ?? '';
//'&access_token=' . $access_token .
        $url = session('callback_url') . '?openid=' . $userInfo['original']['openid'] . '&_t=' . time();
//        dd($url);
//        echo(trim($url));
//        die();
//        header("Location:".$url);exit();
        return Redirect::away($url);
        // 跳转ucenter，并传入openid，ucenter主要维护绑定的手机号,绑定的系统账户等信息
        return redirect('https://www.baidu.com/s?wd=' . urlencode($url));

    }

    public function qrcode()
    {
        return view('we.qrcode');
    }
    public function qrback()
    {

        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $wxuser = $oauth->user();
        dd($wxuser);
        $app =new Application([
            'debug' => true,
            'app_id' => config('wechat.open_platform.weixinweb.app_id'),
            'secret' =>  config('wechat.open_platform.secret.app_id'),
            'oauth' => [
                'scopes'  =>['snsapi_login'],
                'callback'  => 'https://baidu.com',
            ]
        ]);
        $oauth =  $app->oauth->user();
        dd($oauth);
        $url = 'www.baidu.com';
        return Redirect::away($url);
        // 跳转ucenter，并传入openid，ucenter主要维护绑定的手机号,绑定的系统账户等信息
        return redirect('https://www.baidu.com/s?wd=' . urlencode($url));

    }

}
