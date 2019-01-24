<?php

namespace App\Transformers;

use App\Models\Weuser;
use League\Fractal\TransformerAbstract;

class WeuserTransformer extends TransformerAbstract
{
    public function transform(Weuser $weuser)
    {
        return [
            'id' => $weuser->id,
            'user_id' => $weuser->user_id,
            'nickname' => $weuser->nickname,
            'sex' => $weuser->sex,
            'language' => $weuser->language ,
            'city' => $weuser->city ,
            'province'=>$weuser->province,
            'country'=>$weuser->country,
            'headimgurl'=>$weuser->headimgurl,
            'created_at' => $weuser->created_at->toDateTimeString(),
            'updated_at' => $weuser->updated_at->toDateTimeString(),
        ];
    }
}