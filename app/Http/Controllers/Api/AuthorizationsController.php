<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Ccrps\ActionsController;
use App\Http\Requests\Api\AppPhoneAuthorizationRequest;
use App\Http\Requests\Api\PhoneAuthorizationRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Http\Requests\Api\WeappAuthorizationRequest;
use App\Http\Requests\Api\WeAuthorizationRequest;
use App\Models\ApiLoginLog;
use App\Models\ApilogUserAgent;
use App\Models\App;
use App\Models\Ccrp\UserLoginLog;
use App\Models\Role;
use App\Models\Weapp;
use App\Models\WeappHasWeuser;
use App\Models\Weuser;
use Auth;
use App\Models\User;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Requests\Api\AuthorizationRequest;

class AuthorizationsController extends Controller
{
    public function phoneStore(PhoneAuthorizationRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }
        $phone = $verifyData['phone'];
        $user = User::where('phone', $phone)->where('phone_verified', 1)->first();
        if (!$user) {
            return $this->response->errorUnauthorized('用户不存在');
        }
        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        $token = Auth::guard('api')->fromUser($user);
        return $this->respondWithToken($token)->setStatusCode(201);
    }

    //通过第三方登录插件登录（需要openid
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        if (!in_array($type, ['weixin'])) {
            return $this->response->errorBadRequest();
        }
//
//        $driver = \Socialite::driver($type);
//
//        try {
//            if ($code = $request->code) {
//                $response = $driver->getAccessTokenResponse($code);
//                $token = array_get($response, 'access_token');
//            } else {
//                $token = $request->access_token;
//                if ($type == 'weixin') {
//                    $driver->setOpenId($request->openid);
//                }
//            }
//
//            $oauthUser = $driver->userFromToken($token);
//        } catch (\Exception $e) {
//            return $this->response->errorUnauthorized('参数错误，请重新登录');
//        }

        switch ($type) {
            case 'weixin':
                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;
                if ($unionid) {
                    $hasUser = WeappHasWeuser::where('unionid', $unionid)->first();
                } else {
                    $hasUser = WeappHasWeuser::where('openid', $oauthUser->getId())->first();
                }

                // 没有用户，默认创建一个用户
                if (!$hasUser) {
                    return $this->response->errorUnauthorized('用户不存在');
//                    $user = User::create([
//                        'name' => $oauthUser->getNickname(),
//                        'avatar' => $oauthUser->getAvatar(),
//                        'weixin_openid' => $oauthUser->getId(),
//                        'weixin_unionid' => $unionid,
//                    ]);
                } else {
                    $user = $hasUser->weuser->user;
                }

                break;
        }

        $token = Auth::guard('api')->fromUser($user);
        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function weappStore($weapp = 'default', WeappAuthorizationRequest $request)
    {
        $code = $request->code;

        $weapp_id = Weapp::miniProgram[$weapp] ?? 'default';
        $weapp_app = Weapp::find($weapp_id);
        $config = $weapp_app->config();

        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = Factory::miniProgram($config);
        $data = $miniProgram->auth->session($code);
//  "session_key" => "EcIPGiWJDW7zueqgnJgyJA=="
//  "expires_in" => 7200
//  "openid" => "oozgd0WhYY2_XN3utLcHqYErRiOQ"
//  "unionid" => "o-zI_uJQvX8vh43xjdJ6iSNfy2ao"

        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code 不正确');
        }

        // 找到 openid 对应的用户
        $openid = $data['openid'];
        $hasWeuser = WeappHasWeuser::where('openid', $openid)->first();
        if (!$hasWeuser) {
            // 改用检测we.unionid 是否存
            if (isset($data['unionid'])) {
                $hasWeuser = WeappHasWeuser::where('unionid', $data['unionid'])->first();
            }
            // 有unionid 但是没有openid，则为小程序等第二应用的用户，插入关系
            if ($hasWeuser) {
                $new_weappHasWeuser = [
                    'weapp_id' => Weapp::miniProgram[$weapp],
                    'weuser_id' => $hasWeuser->weuser_id,
                    'openid' => $data['openid'],
                    'unionid' => $data['unionid'],
                ];
                $new_weappHasWeuser = new WeappHasWeuser($new_weappHasWeuser);
                $new_weappHasWeuser->save();
                $user = $hasWeuser->weuser->user;
                if (config('api.defaults.auto_register_tester')) {
                    $user->registerTester();
                }
            } else {
                if ($request->userInfo) {
                    $userInfo = $request->userInfo;
                    // 如果openid不存在，创建user,
                    $new_user = [
                        'name' => $userInfo['nickName']
                    ];
                    $user = new User($new_user);
                    $user->save();

                    $new_weuser = [
                        'nickname' => $userInfo['nickName'],
                        'sex' => $userInfo['gender'],
                        'language' => $userInfo['language'] ?? "",
                        'city' => $userInfo['city'] ?? "",
                        'province' => $userInfo['province'] ?? "",
                        'country' => $userInfo['country'] ?? "",
                        'headimgurl' => $userInfo['avatarUrl'] ?? "",
                        'privilege' => ''
                    ];
                    $weuser = new Weuser($new_weuser);
                    $user->weuser()->save($weuser);
                    $new_weappHasWeuser = [
                        'weapp_id' => Weapp::miniProgram[$weapp],
                        'openid' => $data['openid'],
                        'unionid' => $data['unionid'] ?? null,
                        'weuser_id' => $user->weuser->id,
                    ];
                    $weappHasWeuser = new WeappHasWeuser($new_weappHasWeuser);
                    $weappHasWeuser->save();
                    if (config('api.defaults.auto_register_tester')) {
                        $user->registerTester();
                    }
                } else {
                    return $this->response->errorUnauthorized('用户不存在');
                }
            }
        } else {
            $user = $hasWeuser->weuser->user;
        }
        $token = Auth::guard('api')->fromUser($user);

        $this->AddLoginLog($request, $user);
        return $this->respondWithToken($token, ['info' => $data])->setStatusCode(201);
    }

    //网页api认证接口
    public function weStore(WeAuthorizationRequest $request)
    {
        $openid = $request->openid;
        $hasUser = WeappHasWeuser::where('openid', $openid)->first();
        if (!$hasUser) {
            return $this->response->errorUnauthorized('用户不存在');
        } else {
            $user = $hasUser->weuser->user;
        }
        $token = Auth::guard('api')->fromUser($user);
        $this->AddLoginLog($request, $user);
        return $this->respondWithToken($token)->setStatusCode(201);
    }

    /**
     * 用户名密码登录
     * @param AuthorizationRequest $request
     */
    public function store(AuthorizationRequest $request)
    {
        $username = $request['username'] ?? $request->username;

        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['username'] = $username;

        $credentials['password'] = $request['password'] ?? $request->password;
        if (!$token = \Auth::guard('api')->attempt($credentials)) {
            return $this->response->errorUnauthorized('用户名或密码错误');
        }

        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ])->setStatusCode(201);
    }

    public function store2(AuthorizationRequest $request)
    {
        $username = $request->username;
//
//        filter_var($username, FILTER_VALIDATE_EMAIL) ?
//            $credentials['username'] = $username :
//            $credentials['name'] = $username;

        $credentials['username'] = $username;
        $credentials['password'] = $request->password;
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return $this->response->errorUnauthorized('用户名或密码错误');
        }

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    public function destroy()
    {
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }

    protected function respondWithToken($token, $withInfo = null)
    {
        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ];

        if ($withInfo) {
            $data = array_merge($data, $withInfo);
        }
        return $this->response->array($data);
    }

    private function AddLoginLog($request, $user)
    {
        if (env('APP_ENV') == 'production') {
            ApiLoginLog::addLog($request, $user);
            UserLoginLog::addCcrpLoginLog($request, $user);
        }

    }

    //ccrp 用户名密码登陆
    public function AppPhoneStore(AppPhoneAuthorizationRequest $request, $slug)
    {
        $verifyData = \Cache::get($request->verification_key);

        if($request->tester=='lengwang')
        {
            //测试模式,不验证手机验证码
        }else{
            if (!$verifyData) {
                return $this->response->error('验证码已失效', 422);
            }

            if (!hash_equals($verifyData['code'], $request->verification_code)) {
                // 返回401
                return $this->response->errorUnauthorized('验证码错误');
            }
        }
        //check ccrp
        if ($slug == 'ccrp') {
            $app = App::where('slug', 'ccrp')->first();
            $login = (new ActionsController())->anonymous($slug, 'users/login');
            if (isset($login->original)) {
                return $this->response->error($login->original['message'], $login->original['status_code']);
            }

            $phone = $verifyData['phone'] ?? $request->phone;
            $user = User::where('phone', $phone)->whereNotNull('phone')->where('phone_verified', 1)->first();
            if ($phone and !$user) {
                //创建user
                $user = new User();
                $user->name = $phone;
                $user->phone = $phone;
                $user->phone_verified = 1;
                $user->save();
//                return $this->response->errorUnauthorized('用户不存在');
            }
            //使用user登陆
            $has_ccrp = $user->getApp($app->id);
            //check 是否可以绑定
            if ($has_ccrp == null) {
                $folder = ucfirst(strtolower($app->program));
                $userModel = "\\App\\Models\\" . $folder . "\\" . "User";
                $users = new $userModel;
                $users->setApiServer($app);
                $username = $request->username;
                $password = $request->password;
                if (!$users->checkUsername($username)) {
                    return $this->response->error('用户名不存在。', 422);
                }
                $login = $users->checkPassword($username, $password);
                if (!$login) {
                    return $this->response->error('密码错误。', 422);
                }
                if (!$user->isLengwang() and !$users->checkPhone($username, $user->phone, $user)) {
                    return $this->response->error('您的手机号不是本系统的联系人，若需绑定请联系客服。', 422);
                }
                $app_user = \App\Models\Ccrp\User::find($login->id);
                $userCompany = $app_user->userCompany;
                if (!$userCompany) {
                    return $this->response->error('单位状态异常，请联系客服', 422);
                }
                $rs = $app->bind($user, $username, $login->id, $login->unitid);
            }
//                $has_ccrp = $user->getApp($app->id);
//                dd($has_ccrp);


        } else {
            return $this->response->errorUnauthorized('系统不存在!');
        }

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        $token = Auth::guard('api')->fromUser($user);
        return $this->respondWithToken($token)->setStatusCode(201);
    }
}
