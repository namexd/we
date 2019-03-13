<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserQrcodeTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'realname' => $user->realname,
            'phone' => $user->phone ,
            'created_at' => $user->created_at->toDateTimeString(),
        ];
    }
}