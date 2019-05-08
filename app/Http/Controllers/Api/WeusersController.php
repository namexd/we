<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\GenerateFormIdRequest;
use App\Http\Requests\Api\WeuserRequest;
use App\Models\WeappHasWeuser;
use App\Models\WeappUserHasFormId;
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

    //创建小程序form_id
    public function generateFormId(GenerateFormIdRequest $request,WeappUserHasFormId $weappUserHasFormId)
    {
        $weapp_id=$request->get('weapp_id');
        $user = $this->user();
        $weapp_user_id=WeappHasWeuser::where('weapp_id',$weapp_id)->where('weuser_id',$user->id)->first()->id;
        $data=$request->only(['form_id']);
        $data['weapp_user_id']=$weapp_user_id;
        $result=$weappUserHasFormId->add($data);
        if ($result)
        {
            return $this->response->created();

        }else
        {
            return $this->response->errorInternal('更新formid失败');
        }

    }
}
