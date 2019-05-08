<?php

namespace App\Http\Controllers\Api\Ocenter;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Models\Ocenter\WxMember;

class WxmembersController extends BaseController
{
    public function checkPhone($openid)
    {
        $wxmember = WxMember::where('wxcode',$openid)->where('status',1)->where('phone_bind_time','>',0)->first();
        return $wxmember?$this->response->array(['phone'=>$wxmember->phone,'bind_time'=>$wxmember->phone_bind_time]):$this->response->noContent();
    }

    public function bindPhone($openid,$phone)
    {
        $wxmember = WxMember::where('wxcode',$openid)->where('status',1)->first();
        if($wxmember)
        {
            $wxmember->phone = $phone;
            $wxmember->phone_bind_time = time();
            $wxmember->save();
        }
        return $wxmember?$this->response->array(['openid'=>$openid,'phone'=>$wxmember->phone,'bind_time'=>$wxmember->phone_bind_time]):$this->response->noContent();
    }

}
