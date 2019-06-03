<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Warninger;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class WarningerTransformer extends TransformerAbstract
{
    public function transform(Warninger $setting)
    {
        $rs =  [
            'id' => $setting->warninger_id,
            'warninger_name' => $setting->warninger_name,
            'warninger_type' => $setting->warninger_type,
            'warninger_body' => $setting->warninger_body,
            'warninger_body_level2' => $setting->warninger_body_level2,
            'warninger_body_level3' => $setting->warninger_body_level3,
            'bind_times' => $setting->bind_times,
            'created_at' =>$setting->ctime?Carbon::createFromTimestamp($setting->ctime)->toDateTimeString():'',
        ];
        if(request()->get('with'))
        {
            $rs['meta'] = ['header' => $setting->warninger_name];
        }
        return $rs;
    }
}