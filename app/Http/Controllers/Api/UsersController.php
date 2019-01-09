<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserPhoneRequest;
use App\Models\User;
use App\Transformers\UserTransformer;

class UsersController extends Controller
{
    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }

    public function index()
    {
        $users = User::get();
        return $this->response->collection($users,new UserTransformer());
    }
    public function phoneUpdate(UserPhoneRequest $request)
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


        $phone_exist = User::where('phone',$verifyData['phone'])->count();
        if($phone_exist){
            return $this->response->error('手机号被占用了，请更换手机号或者联系客服解绑',422);
        }


        $user = User::where('id',$this->user()->id)->first();
        $user->phone = $verifyData['phone'];
        $user->phone_verified = 1;
        $user->realname = $request->realname;
        $user->save();
        // 清除验证码缓存
        \Cache::forget($request->verification_key);
        return $this->response->created(null,$user);
    }
}
