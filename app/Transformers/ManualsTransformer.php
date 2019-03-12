<?php

namespace App\Transformers;

use App\Models\Manual;
use League\Fractal\TransformerAbstract;

class ManualsTransformer extends TransformerAbstract
{
    public function transform(Manual $manual)
    {
        $rs = [
            'id' => $manual->id,
            'title' => $manual->title,
            'type' => $manual->type,
            'description' => $manual->description,
            'slug' => $manual->slug ,
            'status'=>$manual->status,
            'image'=>env('ALIYUN_OSS_URL').$manual->image,
            'created_at' => $manual->created_at->toDateTimeString(),
            'updated_at' => $manual->updated_at->toDateTimeString(),
        ];
        return  $rs;
    }

}