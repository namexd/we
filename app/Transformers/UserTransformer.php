<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'realname' => $user->realname,
            'phone' => $user->phone ,
            'phone_verified' => $user->phone_verified ,
            'company_id' => $user->company_id ,
            'company_ids' => $user->company_ids ,
            'headimgurl'=>$user->weuser->headimgurl,
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }
}