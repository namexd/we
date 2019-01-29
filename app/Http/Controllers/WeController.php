<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Request;
use App\Models\User;
use App\Models\Weapp;
use App\Models\WeappHasWeuser;
use App\Models\Weuser;
use function App\Utils\add_query_param;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Redirect;
use EasyWeChat\Factory;

/**
 * 智慧冷链公众号：网页版微信授权
 * Class WeController
 * @package App\Http\Controllers
 */
class WeController extends Controller
{
    private $weapp_id = Weapp::智慧冷链公众号;
    private $﻿redirect_url = '/we/qrcode/home';

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

    public function qrcode($redirect_url = null)
    {
        $redirect_url = $redirect_url ?? base64_encode($this->﻿redirect_url);
        request()->session()->put('qrback_url', $redirect_url);
        $redirect_uri = route('we.qrback', ['redirect_url' => $redirect_url]);
        $redirect_uri = urlencode($redirect_uri);//该回调需要url编码
        $appID = config('wechat.open_platform.weixinweb.app_id');
        $scope = "snsapi_login";//写死，微信暂时只支持这个值
//准备向微信发请求
        $url = "https://open.weixin.qq.com/connect/qrconnect?appid=" . $appID . "&redirect_uri=" . $redirect_uri
            . "&response_type=code&scope=" . $scope . "&state=STATE#wechat_redirect";
//请求返回的结果(实际上是个html的字符串)
        $client = new Client();
        $result = $client->get($url);
//替换图片的src才能显示二维码
        $result = str_replace("/connect/qrcode/", "https://open.weixin.qq.com/connect/qrcode/", $result->getBody());
        return $result; //返回页面
    }

    public function qrback()
    {
        $code = request()->code ?? null;
        $appid = config('wechat.open_platform.weixinweb.app_id');
        $secret = config('wechat.open_platform.weixinweb.secret');
        $token = '';
        if (session('qrback_url')) {
            $this->﻿redirect_url = session('qrback_url');
        }
        if (!empty($code))  //有code
        {
            //通过code获得 access_token + openid
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid
                . "&secret=" . $secret . "&code=" . $code . "&grant_type=authorization_code";
            $client = new Client();
            $jsonResult = $client->get($url);
            $resultArray = json_decode($jsonResult->getBody(), true);
            if (!isset($resultArray["access_token"])) {
                return Redirect::route('we.qrcode', ['﻿redirect_url' => $this->﻿redirect_url]);
            }
            $access_token = $resultArray["access_token"];
            $openid = $resultArray["openid"];
            //通过access_token + openid 获得用户所有信息,结果全部存储在$infoArray里,后面再写自己的代码逻辑
            $infoUrl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
            $infoResult = $client->get($infoUrl);
            $userInfo = json_decode($infoResult->getBody(), true);


            //登录或创建用户
            // 检测we.openid 是否存
            $hasWeuser = WeappHasWeuser::where('weapp_id', Weapp::智慧冷链用户中心)->where('openid', $userInfo['openid'])->first();
            if (!$hasWeuser) {
                // 改用检测we.unionid 是否存
                $hasWeuser = WeappHasWeuser::where('unionid', $userInfo['unionid'])->first();
                // 有unionid 但是没有openid，则为小程序等第二应用的用户，插入关系
                if ($hasWeuser) {
                    $new_weappHasWeuser = [
                        'weapp_id' => $this->weapp_id,
                        'weuser_id' => $hasWeuser->weuser_id,
                        'openid' => $userInfo['openid'],
                        'unionid' => $userInfo['unionid'],
                    ];
                    $new_weappHasWeuser = new WeappHasWeuser($new_weappHasWeuser);
                    $new_weappHasWeuser->save();
                }
            }
            if ($hasWeuser) {
                // 如果openid存在，检测unionid字段是否为空，为空则更新
                if (!$hasWeuser->unionid and $userInfo['unionid']) {
                    $hasWeuser->unionid = $userInfo['unionid'];
                    $hasWeuser->save();
                }

                $weuser = $hasWeuser->weuser;
                $user = $weuser->user;
                $token = Auth::guard('api')->fromUser($user);
            }
            $url = base64_decode($this->﻿redirect_url);
            $url = add_query_param($url, 'token', $token);
            $url = add_query_param($url, '_t', time());
            return Redirect::away($url);
        } else {
            abort(302, '微信授权失败 ：(');
        }
    }

    public function qrhome()
    {
        echo '获取到token：<hr>';
        echo '<pre>';
        echo request()->token;
        echo '</pre>';
        echo '<hr>如何扫码登录到指定地址？<br/>传递一个base64_encode(URL)给qrcode，如跳到百度：`https://we.coldyun.net/we/qrcode/aHR0cDovL3d3dy5iYWlkdS5jb20=`<hr>';

    }

}
