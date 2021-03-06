<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Request;
use App\Models\App;
use App\Models\Ccrp\Company;
use App\Models\Ocenter\WxMember;
use App\Models\User;
use App\Models\UserHasApp;
use App\Models\Weapp;
use App\Models\WeappHasWeuser;
use App\Models\WechatMedia;
use App\Models\Weuser;
use function App\Utils\add_query_param;
use function App\Utils\app_access_decode;
use Dingo\Api\Auth\Provider\JWT;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Redirect;
use EasyWeChat\Factory;
use URL;

/**
 * 智慧冷链公众号：网页版微信授权
 * Class WeController
 * @package App\Http\Controllers
 */
class WeController extends Controller
{
    //默认页面
    private $redirect_url = '/we/qrhome';
    //首页
    private $redirect_home = 'home';
    private $redirect_app = '';
    private $redirect_ucenter = 'https://we.coldyun.net/ucenter/#/login';

    public function test2()
    {
        dd(1);
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $data = $app->material->list('news', 0, 20);
//return;
        $medias = [];
        $insert = 0;
        if ($data) {
            foreach ($data['item'] as $item) {
                $media_id = $item['media_id'];
                $exist = WechatMedia::where('media_id', $media_id)->count();
                if ($exist == 0) {
                    $content = $item['content'];
                    $create_time = $content['create_time'];
                    $update_time = $content['update_time'];
                    foreach ($content['news_item'] as $new_item) {
                        $meida = [];
                        $media['media_id'] = $media_id;
                        $media['create_time'] = $create_time;
                        $media['update_time'] = $update_time;
                        $media['title'] = $new_item['title'];
                        $media['author'] = $new_item['author'];
                        $media['digest'] = $new_item['digest'];
                        $media['content'] = $new_item['content'];
                        $media['content_source_url'] = $new_item['content_source_url'];
                        $media['thumb_media_id'] = $new_item['thumb_media_id'];
                        $media['show_cover_pic'] = $new_item['show_cover_pic'];
                        $media['url'] = $new_item['url'];
                        $media['thumb_url'] = $new_item['thumb_url'];
                        $mediaer = WechatMedia::create($media);
                        $medias[] = $mediaer;
                        $insert++;

                    }
                }
            }
            echo $insert . '条插入了。';
            dd(count($medias));
        }
    }

    /**
     * 微信网页授权登录
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function oauth()
    {
        $scopes = 'snsapi_base';//'snsapi_userinfo';//
        if (request('scopes')) {
            $scopes = request('scopes');
        }
        if (request('redirect_url')) {
            request()->session()->put('callback_url', urldecode(request('redirect_url')));
        } else {
            request()->session()->put('callback_url', $this->redirect_url);
        }
        $config = config('wechat.official_account.default');
        $config['oauth']['scopes'] = $scopes;
        $app = Factory::officialAccount($config);

        $oauth = $app->oauth;
//        $oauth = $app->oauth;
        return $oauth->redirect();
    }

    /**
     * 微信网页授权登录回调
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function callback()
    {

        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $wxuser = $oauth->user();
        $userInfo = $wxuser->toArray();

        // 检测we.openid 是否存
        $hasWeuser = WeappHasWeuser::where('weapp_id', Weapp::智慧冷链公众号)->where('openid', $userInfo['original']['openid'])->first();
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
                    'weapp_id' => Weapp::智慧冷链公众号,
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
                'weapp_id' => Weapp::智慧冷链公众号,
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
//        return redirect('https://www.baidu.com/s?wd=' . urlencode($url));

    }

    /**
     * 扫码登录
     * @param null $redirect_url
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function qrcode($redirect_url = null)
    {
        $apps = App::pluck('app_url', 'slug')->toArray();
        if ($redirect_url and in_array($redirect_url, array_keys($apps))) {
            $redirect_url = ['app' => $redirect_url];
        } elseif (base64_decode($redirect_url)) {
            $redirect_url = ['url' => $redirect_url];
        } else {
            $redirect_url = ['url' => base64_encode($this->redirect_url)];
        }
        request()->session()->put('qrback_url', $redirect_url);
        if (request()->get('qrback')) {
            $redirect_uri = route(request()->qrback, ['redirect_url' => $redirect_url]);
        } else {
            $redirect_uri = route('we.qrback', ['redirect_url' => $redirect_url]);
        }
        $redirect_uri = urlencode($redirect_uri);//该回调需要url编码

        $appID = config('wechat.open_platform.weixinweb.app_id');

        if ((request()->get('method')) == 'js') {
            return view('we/qrcode', ['redirect_uri' => $redirect_uri]);
            exit();
        }

        $scope = "snsapi_login";//写死，微信暂时只支持这个值
//准备向微信发请求
        $url = "https://open.weixin.qq.com/connect/qrconnect?appid=" . $appID . "&redirect_uri=" . $redirect_uri
            . "&response_type=code&scope=" . $scope . "&state=STATE&href=data:text/css;base64,LmltcG93ZXJCb3ggLnFyY29kZSB7d2lkdGg6IDIwMHB4O30NCi5pbXBvd2VyQm94IC50aXRsZSB7ZGlzcGxheTogbm9uZTt9DQouaW1wb3dlckJveCAuaW5mbyB7d2lkdGg6IDIwMHB4O30NCi5zdGF0dXNfaWNvbiB7ZGlzcGxheTpub25lfQ0KLmltcG93ZXJCb3ggLnN0YXR1cyB7dGV4dC1hbGlnbjogY2VudGVyO30=";
//请求返回的结果(实际上是个html的字符串)
        $client = new Client();
        $result = $client->get($url);
//替换图片的src才能显示二维码
        $result = str_replace("/connect/qrcode/", "https://open.weixin.qq.com/connect/qrcode/", $result->getBody());
        return $result; //返回页面
    }

    /**
     * 扫码登录回调
     * @return \Illuminate\Http\RedirectResponse
     */
    public function qrback()
    {
        $code = request()->code ?? null;
        $appid = config('wechat.open_platform.weixinweb.app_id');
        $secret = config('wechat.open_platform.weixinweb.secret');
        $token = '';
        if (session('qrback_url')) {
            $redirect = session('qrback_url');
            if (isset($redirect['app'])) {
                $this->redirect_app = $redirect['app'];
            }
            if (isset($redirect['url'])) {
                $this->redirect_url = $redirect['url'];
            }
        }
        if ($code)  //有code
        {
            //通过code获得 access_token + openid
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid
                . "&secret=" . $secret . "&code=" . $code . "&grant_type=authorization_code";
            $client = new Client();
            $jsonResult = $client->get($url);
            $resultArray = json_decode($jsonResult->getBody(), true);
            if (!isset($resultArray["access_token"])) {
                return Redirect::route('we.qrcode', ['redirect_url' => $this->redirect_url]);
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
                        'weapp_id' => Weapp::智慧冷链用户中心,
                        'weuser_id' => $hasWeuser->weuser_id,
                        'openid' => $userInfo['openid'],
                        'unionid' => $userInfo['unionid'],
                    ];
                    $new_weappHasWeuser = new WeappHasWeuser($new_weappHasWeuser);
                    $new_weappHasWeuser->save();
                } else {
                    $new_user = [
                        'name' => $userInfo['nickname']
                    ];
                    //扫码 create user
                    $user = new User($new_user);
                    $user->save();
                    //扫码 create weuser

                    $new_weuser = [
                        'nickname' => $userInfo['nickname'],
                        'sex' => $userInfo['sex'],
                        'language' => $userInfo['language'],
                        'city' => $userInfo['city'],
                        'province' => $userInfo['province'],
                        'country' => $userInfo['country'],
                        'headimgurl' => $userInfo['headimgurl'],
                        'privilege' => json_encode($userInfo['privilege'])
                    ];
                    $weuser = new Weuser($new_weuser);
                    $user->weuser()->save($weuser);
                    $new_weappHasWeuser = [
                        'weapp_id' => Weapp::智慧冷链用户中心,
                        'openid' => $userInfo['openid'],
                        'unionid' => $userInfo['unionid'],
                        'weuser_id' => $user->weuser->id,
                    ];
                    $weappHasWeuser = new WeappHasWeuser($new_weappHasWeuser);
                    $weappHasWeuser->save();
                    $hasWeuser = $weappHasWeuser;
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

                //自动绑定
                $auto_bind_app = session('auto_bind_app') ?? false;
                if ($auto_bind_app) {

                    $user_has_app = UserHasApp::where('app_id', $auto_bind_app['app_id'])
                        ->where('user_id', $user->id)
                        ->first();
                    if (!$user_has_app) {
                        $app = App::where('id', $auto_bind_app['app_id'])->first();
                        $app->bind($user, $auto_bind_app['app_username'], $auto_bind_app['app_userid'], $auto_bind_app['app_unitid']);
                        request()->session()->forget('auto_bind_app');
                    }
                }

                //生成token、
                $token = Auth::guard('api')->fromUser($user);
                $expires_in = Auth::guard('api')->factory()->getTTL() * 60;
                if ($this->redirect_app) {

                    //检查用户是否验证过手机号
                    if ($user->phone_verified == 0) {
                        $url = $this->redirect_ucenter;
                        $url = add_query_param($url, 'need_phone_verified', 1);
                        $url = add_query_param($url, 'token', $token);
                        $url = add_query_param($url, 'expires_in', $expires_in);
                        $url = add_query_param($url, '_t', time());
                        $url = add_query_param($url, 'message', '手机号未验证');
                    } else {
                        // 自动登录到第三方系统，追加access
                        $user_info = (new App())->userBindedLoginInfo($this->redirect_app, $user);
                        if ($user_info and $user_info['access']) {
                            if ($user_info['login_url'] != '') {
                                $url = $user_info['login_url'] . '?access=' . $user_info['access'] . '&';
                            } else {
                                echo '<h1>应用未开启扫码登录地址 :(</h1><hr>';
                                exit();
                            }
                        } else {
                            $url = $this->redirect_home;
                            $url = add_query_param($url, 'need_bind_app', $this->redirect_app);
                            $url = add_query_param($url, 'token', $token);
                            $url = add_query_param($url, 'expires_in', $expires_in);
                            $url = add_query_param($url, '_t', time());
                            $url = add_query_param($url, 'message', ($user_info['app_name'] ?? "") . '系统未绑定');
                        }
                    }

                } else {
                    $url = base64_decode($this->redirect_url);
                    $url = add_query_param($url, 'token', $token);
                    $url = add_query_param($url, 'expires_in', $expires_in);
                    $url = add_query_param($url, '_t', time());
                }
                return Redirect::away($url);
            } else {
                echo '<h1>微信授权失败</h1><hr>';
//                abort(302, '微信授权失败了 ：(');
            }
        } else {
            echo '<h1>微信授权失败 :(</h1><hr>';
//            abort(302, '微信授权失败 ：(');
        }
    }

    /**
     * 扫码登录测试
     */
    public function qrhome()
    {
        $need_bind_app = request()->need_bind_app ?? false;
        if ($need_bind_app) {
            switch ($need_bind_app) {
                case 'ccrp':
                    //check 是否已经绑定了微信公众号的旧版冷链系统
                    $user = Auth::guard('api')->user();
                    $weuser = $user->weuser;
                    $weixin = $weuser->weappHasWeuser;
                    if ($weixin) {
                        $unionid = $weixin->unionid;
                        if ($unionid) {
                            $wxmember = WxMember::where('unionid', $unionid)->where('status', 1)->first();
                            if ($wxmember) {
                                $ccrp_user = \App\Models\Ccrp\User::where('username', $wxmember->username)->first();
                                $ccrp_company = Company::find($ccrp_user->company_id);
                                if ($ccrp_user->status and $ccrp_company->status) {
                                    $app = App::where('slug', App::冷链监测系统)->first();
                                    $bind = $app->bind($user, $ccrp_user->username, $ccrp_user->id, $ccrp_company->id);
                                    if ($bind) {
                                        $user_info = (new App())->userBindedLoginInfo(App::冷链监测系统, $user);
                                        $url = $user_info['login_url'] . '?access=' . $user_info['access'] . '&';

                                        return Redirect::away($url);
                                    }
                                }
                            }
                        }
                    }
                    break;
            }
        }
        $token = request()->token ?? '';
        $expires_in = request()->expires_in ?? '';
        $message = request()->message ?? '';
        $url = $this->redirect_ucenter;
        $url = add_query_param($url, 'need_bind_app', $need_bind_app);
        $url = add_query_param($url, 'token', $token);
        $url = add_query_param($url, 'expires_in', $expires_in);
        $url = add_query_param($url, '_t', time());
        $url = add_query_param($url, 'message', $message);
        return Redirect::away($url);
        //
        echo '<h1>测试·扫码结果页面</h1><hr>';
        echo '1. 获取到token：<hr>';
        echo '<pre>';
        dump(request()->token);
        echo '</pre>';
        $user = $token = Auth::guard('api')->user();
        echo '2. 读取用户信息：';
        echo '<pre>';
        if ($user) {
            dump($user->toArray());
        } else {
            dump('token无效');
        }
        echo '</pre>';
        echo '<hr>3. 如何扫码登录到指定地址？<br/>传递一个base64_encode(URL)给qrcode，如跳到百度：`<b>https://we.coldyun.net/we/qrcode/aHR0cDovL3d3dy5iYWlkdS5jb20=</b>`<hr>';

    }

    /**
     * 生物制品跳转到冷链系统
     * @param string $app
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function jumpLogin($from = 'bpms', $to = 'ccrp')
    {
        $access = request()->get('access') ?? '';
        $from_app = App::where('slug', $from)->first();
        $to_app = App::where('slug', $to)->first();
        if (!$from_app or !$to_app) {
            echo '<h1>' . '登录失败，系统标识错误。' . '</h1><hr>';
            exit();
        }
        $data = app_access_decode($from_app->appkey, $from_app->appsecret, $access);
        if (!$data) {
            echo '<h1>' . '登录失败，access不正确' . '</h1><hr>';
            exit();
        }

        $res['app_id'] = $from_app->id ?? '';
        $res['app_username'] = $data['username'] ?? '';
        $res['app_userid'] = $data['userid'] ?? '';
        $res['app_unitid'] = $data['unitid'] ?? '';
        $user_has_app = UserHasApp::where('app_id', $res['app_id'])
            ->where('app_username', $res['app_username'])
            ->where('app_userid', $res['app_userid'])
            ->where('app_unitid', $res['app_unitid'])
            ->first();

        if ($user_has_app) {
            $user = $user_has_app->user;
            $user_info = (new App())->userBindedLoginInfo($to_app->slug, $user);
            if ($user_info and $user_info['access']) {
                if ($user_info['login_url'] != '') {
                    $url = $user_info['login_url'] . '?access=' . $user_info['access'] . '&';
                    return Redirect::away($url);
                } else {
                    echo '<h1>应用未开启扫码登录地址 :(</h1><hr>';
                    exit();
                }
            } else {
                request()->session()->put('auto_bind_app', $res);
                return redirect(route('we.qrcode', ['redirect_url' => $to_app->slug]));
            }
        } else {
            request()->session()->put('auto_bind_app', $res);
            return redirect(route('we.qrcode', ['redirect_url' => $to_app->slug]));
        }

    }

    /**
     * 生物制品跳转到冷链系统
     * @param string $app
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function jumpLoginFromUcenter($to = 'ccrp')
    {
        $to_app = App::where('slug', $to)->first();
        if (!$to_app) {
            echo '<h1>' . '登录失败，系统标识错误。' . '</h1><hr>';
            exit();
        }

        $user = Auth::guard('api')->user();

        if (!$user) {
            echo '<h1>token错误，请重新登录 :(</h1><hr>';
            exit();
        }
        $user_has_app = $user->getApp($to_app->id);

        if ($user_has_app) {
            $user_info = (new App())->userBindedLoginInfo($to_app->slug, $user);
            if ($user_info and $user_info['access']) {
                if ($user_info['login_url'] != '') {
                    $url = $user_info['login_url'] . '?access=' . $user_info['access'] . '&';
                    return Redirect::away($url);
                } else {
                    echo '<h1>"' . $to_app->name . '"未开启扫码登录地址 :(</h1><hr>';
                    exit();
                }
            } else {
                $res = $user_has_app->toArray();
                request()->session()->put('auto_bind_app', $res);
                return redirect(route('we.qrcode', ['redirect_url' => $to_app->slug]));
            }
        } else {
            return redirect(route('we.qrcode', ['redirect_url' => $to_app->slug]));
        }

    }


    /**
     * 扫码登录测试
     */
    public function bind()
    {


        $refer = URL::previous();
        if (session('user')) {
            $user = session('user');
        } else {
            $user = auth('api')->user();
            if (!$user) {
                return response()->view('we.error', ['message' => '没有获得用户信息,请重新登录.', 'refer' => $refer]);
            } else {
                $user = $user->toArray();
            }
        }

        if ($user) {
            session()->put('user', $user);
        } else {
            return response()->view('we.error', ['message' => '没有获得用户信息,请重新登录.', 'refer' => $refer]);
        }
        session()->put('redirectPath', URL::previous());
        $url = route('we.qrcode', base64_encode(route('we.qrbind'))) . '?qrback=we.qrback.bind';
        return Redirect::away($url);


    }

    /**
     * 扫码登录回调
     * @return \Illuminate\Http\RedirectResponse
     */
    public function qrbackBind()
    {
        $refer = session('redirectPath');
        $user_session = session('user');
        session()->forget('redirectPath');
        session()->forget('user');
        $user = User::find($user_session['id']);
        if (!$user) {
            return response()->view('we.error', ['message' => '没有获得用户信息,请重新登录.']);
        }
        $code = request()->code ?? null;
        $appid = config('wechat.open_platform.weixinweb.app_id');
        $secret = config('wechat.open_platform.weixinweb.secret');
        if (session('qrback_url')) {
            $redirect = session('qrback_url');
            if (isset($redirect['app'])) {
                $this->redirect_app = $redirect['app'];
            }
            if (isset($redirect['url'])) {
                $this->redirect_url = $redirect['url'];
            }
            session()->forget('qrback_url');
        }


        if ($code)  //有code
        {
            //通过code获得 access_token + openid
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid
                . "&secret=" . $secret . "&code=" . $code . "&grant_type=authorization_code";
            $client = new Client();
            $jsonResult = $client->get($url);
            $resultArray = json_decode($jsonResult->getBody(), true);
            if (!isset($resultArray["access_token"])) {
                return Redirect::route('we.qrcode', ['redirect_url' => $this->redirect_url]);
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
                        'weapp_id' => Weapp::智慧冷链用户中心,
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
                $old_user = $weuser->user;
                if ($old_user->phone_verified == 1) {
                    return response()->view('we.error', ['message' => '该微信已经绑定过账号"' . $old_user->name . '(' . $old_user->realname . ')"了', 'refer' =>
                        $refer]);
                }
                if ($weuser and $weuser->user_id != $user->id) {
                    $old_user->status = 0;
                    $old_user->save();

                    $weuser->user_id = $user->id;
                    $weuser->save();
                }

                $user->name = $userInfo['nickname'];
                $user->save();

                return response()->view('we.success', ['message' => '恭喜，' . $user->name . '，你已经绑定成功。', 'refer' =>
                    $refer]);
            } else {
                //create weuser
                $new_weuser = [
                    'nickname' => $userInfo['nickname'],
                    'sex' => $userInfo['sex'],
                    'language' => $userInfo['language'],
                    'city' => $userInfo['city'],
                    'province' => $userInfo['province'],
                    'country' => $userInfo['country'],
                    'headimgurl' => $userInfo['headimgurl'],
                    'privilege' => json_encode($userInfo['privilege'])
                ];
                $weuser = new Weuser($new_weuser);
                $user->weuser()->save($weuser);
                $new_weappHasWeuser = [
                    'weapp_id' => Weapp::智慧冷链用户中心,
                    'openid' => $userInfo['openid'],
                    'unionid' => $userInfo['unionid'],
                    'weuser_id' => $user->weuser->id,
                ];
                $weappHasWeuser = new WeappHasWeuser($new_weappHasWeuser);
                $weappHasWeuser->save();

                $user->name = $userInfo['nickname'];
                $user->save();



                return response()->view('we.success', ['message' => '恭喜，' . $user->name . '，你已经绑定成功啦。', 'refer' =>
                    $refer]);
            }
        } else {
            return response()->view('we.error', ['message' => '微信授权失败', 'refer' =>
                $refer]);
        }
    }
}
