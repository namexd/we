<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserAppRequest;
use App\Http\Requests\Api\UserVerificatioinCodesRequest;
use App\Http\Requests\Api\UserRequest;
use App\Http\Requests\Api\WeuserRequest;
use App\Models\App;
use App\Models\AppRedirect;
use App\Models\Role;
use App\Models\RoleHasUser;
use App\Models\User;
use App\Models\UserHasApp;
use App\Models\Weapp;
use App\Transformers\UserHasAppTransformer;
use App\Transformers\UserQrcodeTransformer;
use App\Transformers\UserTransformer;
use function App\Utils\app_access_encode;
use Dingo\Api\Auth\Auth;
use Illuminate\Http\Request;
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
            $users = (new User())->withRole(Role::LENGWANG_ROLE_ID)->orderBy('realname', 'asc')->get();
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
        if ($user->status == 0) {
            return $this->response->error('您的账号被禁用。', 401);
        }
        if (!$user->phone or !$user->phone_verified) {
            return $this->response->error('您的手机号没有验证。', 456);
        }
        $app = App::where('id',$request->app_id)->where('status',1)->first();
        if (!$app) {
            return $this->response->error('管理系统选择错误。', 422);
        }
        $app_ids = App::where('program',$app->program)->pluck('id');
        $binded = $user->hasApps->whereIn('app_id', $app_ids)->first();
        if ($binded) {
            return $this->response->error('已经使用【' . $binded->app_username . '】绑定了【' . $binded->app->name . '】，如需重新绑定，请先解绑。', 422);
        }
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
        if (!$user->isLengwang() and  !$users->checkPhone($username,$user->phone,$user)) {
            return $this->response->error('您的手机号不是本系统的联系人，若需绑定请联系客服。', 422);
        }
        if($folder=='ccrp')
        {
            //ccrp 定向检测单位状态
            $app_user = \App\Models\Ccrp\User::find( $login->id);
            $userCompany = $app_user->userCompany;
            if (!$userCompany)
            {
                return $this->response->error('单位状态异常，请联系客服', 422);
            }
        }
        $rs = $app->bind($user, $username, $login->id, $login->unitid);
        return $this->response->created(null, $rs->toArray());
    }

    public function autoBindApps($app_id=1,Request $request)
    {
        $user = $this->user();
        if ($user->status == 0) {
            return $this->response->error('您的账号被禁用。', 401);
        }
        if (!$user->phone or !$user->phone_verified) {
            return $this->response->error('您的手机号没有验证。', 456);
        }
        $binded = $user->hasApps->where('app_id', $request->app_id)->first();
        if ($binded) {
            if($binded->app_username == $request->username)
            {
                return $this->response->created(null, $binded->toArray());
            }else{
                $app = App::where('id', $request->app_id)->first();
                $app->unbind($user);
            }
        }
        $app = App::where('id', $app_id)->where('status', 1)->first();
        if (!$app) {
            return $this->response->error('管理系统选择错误。', 422);
        }

        $folder = ucfirst(strtolower($app->program));
        $userModel = "\\App\\Models\\" . $folder . "\\" . "User";
        $users = new $userModel;
        $users->setApiServer($app);
        $username = $request->username;
        if (!$users->checkUsername($username)) {
            return $this->response->error('用户名不存在。', 422);
        }
        if( $login = $users->getByUsername($username))
        {
            $rs = $app->bind($user, $login->username, $login->id, $login->unitid);
        }
        return $this->response->created(null, $rs->toArray());
    }

    public function checkApps($slug)
    {
        $user = $this->user();
        if (!$user->phone or !$user->phone_verified) {
            return $this->response->error('您的手机号没有验证。', 456);
        }
        $app = App::where('program', $slug)->first();
        if (!$app) {
            return $this->response->error('系统请求错误。', 422);
        }
        $binded = $user->hasApps->where('app_id', $app->id)->first();
        if (!$binded) {
            return $this->response->error('未绑定系统。', 457);
        }
        //是否设置了跳转
        $redirect = AppRedirect::where('app_id',$app->id)->where('app_unitid',$binded->app_unitid)->first();
        if ($redirect) {
            return $this->response->created(null,['redirect_url'=>$redirect->redirect_url]);
        }

        return $this->response->created();
    }

    public function unbindApps(UserAppRequest $request)
    {
        $user = $this->user();
        $app_id = $request->app_id;
        $app = App::where('id', $app_id)->first();
        if (!$app) {
            return $this->response->error('管理系统选择错误', 422);
        }
        $app->unbind($user);
        return $this->response->noContent();
    }

    public function appsLoginUrl($app_slug)
    {
        $user = $this->user();
        $array = (new App())->userBindedLoginInfo($app_slug, $user);
        return $this->response->array($array);
    }

    /**
     * 用户生成二维码等数据
     * @return \Dingo\Api\Http\Response
     */
    public function qrcode()
    {
        if ($this->user()->phone_verified == 0) {
            return $this->response->error('手机号未验证', 456);
        }
        $user = $this->user();
        $info = ['id' => $user->id];
        return $this->response->array(
            [
                'code' => \App\Utils\encrypt(json_encode($info), 'qrcode'),
                'image' => $user->weuser->headimgurl
            ]);
    }

    public function qrcodeShow($code)
    {
        $info = json_decode(\App\Utils\decrypt($code, 'qrcode'), true);
        if ($info['id']) {
            $user = User::find($info['id']);
            return $this->response->item($user, new UserQrcodeTransformer());
        }
        return $this->response->noContent();
    }

    public function getBindMiniProgram($company_id='2441')
    {
        $userCount=UserHasApp::query()->where('app_unitid',$company_id)->where('app_id',1)->whereHas('user',function ($query){
           $query->whereHas('weuser',function ($query){
               $query->whereHas('weappHasWeusers',function ($query){
                  $query->where('weapp_id',Weapp::壹苗链小程序);
               });
           }) ;
        })->count();
        return $this->response->array(['count'=>$userCount]);
    }

    public function getCertification($company_id='2441')
    {
        $userCount=UserHasApp::query()->where('app_unitid',$company_id)->where('app_id',1)->whereHas('user',function ($query){
            $query->where('phone_verified',1);
        })->count();
        return $this->response->array(['count'=>$userCount]);
    }

}
