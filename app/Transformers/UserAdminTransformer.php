<?php

namespace App\Transformers;

use App\Models\User;
use function App\Utils\hidePhone;
use League\Fractal\TransformerAbstract;

class UserAdminTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        $apps = $user->apps->pluck('name')->toArray();
        $weuser = $user->weuser;
        return [
            'id' => $user->id,
            'name' => $user->name,
            'realname' => $user->realname,
            'phone' => hidePhone($user->phone) ,
            'phone_verified' => $user->phone_verified ,
            'headimgurl'=>$weuser->headimgurl,
            'bind_apps'=>implode(',',$apps),
            'region'=> $weuser->country . ',' . $weuser->province . ',' . $weuser->city,
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }
}