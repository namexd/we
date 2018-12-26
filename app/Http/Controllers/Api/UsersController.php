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
        $user = User::where('id',$this->user()->id)->first();
        $user->phone = $verifyData['phone'];
        $user->phone_verified = 1;
        $user->realname = $request->realname;
        $user->save();
        // 清除验证码缓存
        \Cache::forget($request->verification_key);
        return $this->response->created();
    }
}
