<?php

namespace App\Transformers;

use App\Models\User;
use function App\Utils\hidePhone;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        $rs= [
            'id' => $user->id,
            'name' => $user->name,
            'realname' => $user->realname,
            'phone' => $user->phone,
            'phone_verified' => $user->phone_verified ,
            'headimgurl'=>$user->weuser?$user->weuser->headimgurl:null,
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
        if(!$user->weuser)
        {
            $rs['weuser'] = null;
            $rs['weuser_qrcode_bind_url'] =route('we.qrbind');
        }else{
            $rs['weuser'] = $user->weuser;
        }
        if(request()->get('with'))
        {
            unset($rs['phone_verified']);
        }
        return $rs;
    }
}