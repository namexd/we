<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserAppRequest;
use App\Http\Requests\Api\UserVerificatioinCodesRequest;
use App\Http\Requests\Api\UserRequest;
use App\Http\Requests\Api\WeuserRequest;
use App\Models\App;
use App\Models\Role;
use App\Models\RoleHasUser;
use App\Models\User;
use App\Models\UserHasApp;
use App\Transformers\UserHasAppTransformer;
use App\Transformers\UserTransformer;
use function App\Utils\app_access_encode;
use Dingo\Api\Auth\Auth;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersController extends Controller
{
    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }

    public function index()
    {
        $user = $this->user();

        if ($user->roles->contains(Role::LENGWANG_ROLE_ID)) {
            $users = (new User())->withRole(Role::LENGWANG_ROLE_ID)->get();
        } else {
            $users = null;
        }
        return $this->response->collection($users, new UserTransformer());
    }

    public function update(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }
        //监测手机号是否被占用
        $phone_exist = User::where('phone', $verifyData['phone'])->count();
        if ($phone_exist) {
            return $this->response->error('手机号被占用了，请更换手机号或者联系客服解绑', 422);
        }

        $user = User::where('id', $this->user()->id)->first();
        $user->phone = $verifyData['phone'];
        $user->phone_verified = 1;
        $user->realname = $request->realname;
        $user->save();
        // 清除验证码缓存
        \Cache::forget($request->verification_key);
        return $this->response->created(null, $user);
    }

    //提交验证码，修改手机号
    public function verificationCodes(UserVerificatioinCodesRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }
        //监测手机号是否被占用
        $phone_exist = User::where('phone', $verifyData['phone'])->count();
        if ($phone_exist) {
            return $this->response->error('手机号被占用了，请更换手机号或者联系客服解绑', 422);
        }

        $user = User::where('id', $this->user()->id)->first();
        $user->phone = $verifyData['phone'];
        $user->phone_verified = 1;
        $user->realname = $request->realname;
        $user->save();
        // 清除验证码缓存
        \Cache::forget($request->verification_key);
        return $this->response->created(null, $user);
    }

    public function apps()
    {
        $user = $this->user();
        $apps = $user->hasApps;
        return $this->response->collection($apps, new UserHasAppTransformer());
    }

    public function bindApps(UserAppRequest $request)
    {
        $user = $this->user();
        if (!$user->phone or !$user->phone_verified) {
            return $this->response->error('您的手机号没有验证', 456);
        }
        $binded = $user->hasApps->where('app_id', $request->app_id)->first();
        if ($binded) {
            return $this->response->error('已经使用【' . $binded->app_username . '】绑定了【' . $binded->app->name . '】，如需重新绑定，请先解绑', 422);
        }
        $app_id = $request->app_id;
        $app = App::where('id', $app_id)->where('status', 1)->first();
        if (!$app) {
            return $this->response->error('管理系统选择错误', 422);
        }

        $folder = ucfirst(strtolower($app->slug));
        $userModel = "\\App\\Models\\" . $folder . "\\" . "User";
        $users = new $userModel;
        $username = $request->username;
        $password = $request->password;
        if (!$login = $users->checkUsername($username)) {
            return $this->response->error('用户名不存在', 422);
        }
        if (!$users->checkPassword($login, $password)) {
            return $this->response->error('密码错误', 422);
        }
        $rs = $app->bind($user, $login->username, $login->id, $login->unitid);
        return $this->response->created(null, $rs->toArray());
    }

    public function unbindApps(UserAppRequest $request)
    {
        $user = $this->user();
        $app_id = $request->app_id;
        $app = App::where('id', $app_id)->first();
        if (!$app) {
            return $this->response->error('管理系统选择错误', 422);
        }
        UserHasApp::where('app_id', $app_id)->where('user_id', $user->id)->delete();
        $role = Role::where('slug', $app->slug)->first();
        if ($role) {
            RoleHasUser::where('role_id', $role->id)->where('user_id', $user->id)->delete();
        }
        return $this->response->noContent();
    }

    public function appsLoginUrl($app_slug)
    {
        $user = $this->user();
        $array = (new App())->userBindedLoginInfo($app_slug, $user);
        return $this->response->array($array);
    }

}
