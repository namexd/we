<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\WeuserRequest;
use App\Transformers\UserTransformer;
use App\Transformers\WeuserTransformer;

class WeusersController extends Controller
{
    public function show()
    {
        $user = $this->user();
        $weuser = $user->weuser;
        return $this->response->item($weuser,new WeuserTransformer());
    }
    public function store(WeuserRequest $request)
    {
        $userInfo = $request->userInfo;
        $user = $this->user();
        $weuser = $user->weuser;
        $weuser->nickname =  $userInfo['nickName'];
        $weuser->sex =  $userInfo['gender'];
        $weuser->language =  $userInfo['language'];
        $weuser->city =  $userInfo['city'];
        $weuser->province =  $userInfo['province'];
        $weuser->country =  $userInfo['country'];
        $weuser->headimgurl =  $userInfo['avatarUrl'];
        $weuser->privilege =  '';
        $weuser->save();
        $user->name = $userInfo['nickName'];
        $user->save();

        return $this->response->item($user, new UserTransformer());
    }
}
