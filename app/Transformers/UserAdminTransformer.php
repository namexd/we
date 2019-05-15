<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserAdminTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        $apps = $user->apps->pluck('name')->toArray();
        return [
            'id' => $user->id,
            'name' => $user->name,
            'realname' => $user->realname,
            'phone' => $user->phone ,
            'phone_verified' => $user->phone_verified ,
            'headimgurl'=>$user->weuser->headimgurl,
            'bind_apps'=>implode(',',$apps),
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }
}