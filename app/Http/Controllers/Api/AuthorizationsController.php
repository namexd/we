<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Http\Requests\Api\WeappAuthorizationRequest;
use App\Http\Requests\Api\WeAuthorizationRequest;
use App\Models\WeappHasWeuser;
use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\AuthorizationRequest;

class AuthorizationsController extends Controller
{

    //通过第三方登录插件登录（需要openid
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        if (!in_array($type, ['weixin'])) {
            return $this->response->errorBadRequest();
        }

        $driver = \Socialite::driver($type);

        try {
            if ($code = $request->code) {
                $response = $driver->getAccessTokenResponse($code);
                $token = array_get($response, 'access_token');
            } else {
                $token = $request->access_token;
                if ($type == 'weixin') {
                    $driver->setOpenId($request->openid);
                }
            }

            $oauthUser = $driver->userFromToken($token);
        } catch (\Exception $e) {
            return $this->response->errorUnauthorized('参数错误，请重新登录');
        }

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

    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;

        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code 不正确');
        }

        // 找到 openid 对应的用户
        $openid = $data['openid'];
        $hasUser = WeappHasWeuser::where('openid', $openid)->first();
        if (!$hasUser) {
                // 如果未提交用户名密码，403 错误提示
                if (!$request->username) {
                    return $this->response->errorForbidden('用户不存在');
                }

//                $username = $request->username;
//
//                // 用户名可以是邮箱或电话
//                filter_var($username, FILTER_VALIDATE_EMAIL) ?
//                    $credentials['email'] = $username :
//                    $credentials['phone'] = $username;
//
//                $credentials['password'] = $request->password;
//
//                // 验证用户名和密码是否正确
//                if (!Auth::guard('api')->once($credentials)) {
//                    return $this->response->errorUnauthorized('用户名或密码错误');
//                }
//                // 获取对应的用户
//                $user = Auth::guard('api')->getUser();
//                $attributes['weapp_openid'] = $data['openid'];
            return $this->response->errorUnauthorized('用户不存在');
        } else {
            $user = $hasUser->weuser->user;
        }
        $token = Auth::guard('api')->fromUser($user);
        return $this->respondWithToken($token)->setStatusCode(201);
    }

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
        return $this->respondWithToken($token)->setStatusCode(201);
    }

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

    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
