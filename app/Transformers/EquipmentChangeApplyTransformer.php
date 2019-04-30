<?php

namespace App\Transformers;

use App\Models\EquipmentChangeApply;
use League\Fractal\TransformerAbstract;

class EquipmentChangeApplyTransformer extends TransformerAbstract
{
    public function transform(EquipmentChangeApply $apply)
    {
        $rs = [
            'id' => $apply->id,
            'company'=>$apply->company->title,
            'phone'=>$apply->phone,
            'apply_time'=>$apply->apply_time,
            'user_id'=>$apply->user_id,
            'user_name'=>$apply->user_name,
            'user_sign'=>$apply->user_sign,
            'details'=>$apply->detail,
            'news'=>$apply->new,
            'status'=>$apply->status,
            'status_name'=>$apply::STATUS[$apply->status],
        ];
        return  $rs;
    }
}