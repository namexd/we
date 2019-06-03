<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Warninger;
use function App\Utils\hidePhone;
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
            'warninger_body_level3' =>  $setting->warninger_body_level3,
            'bind_times' => $setting->bind_times,
            'created_at' =>$setting->ctime?Carbon::createFromTimestamp($setting->ctime)->toDateTimeString():'',
        ];
        if( in_array($rs['warninger_type'],['短信','电话']))
        {
            $rs['warninger_body'] = hidePhone($setting->warninger_body);
            $rs['warninger_body_level2'] = hidePhone($setting->warninger_body_level2);
            $rs['warninger_body_level3'] = hidePhone($setting->warninger_body_level3);
        }
        if(request()->get('with'))
        {
            $rs['meta'] = ['header' => $setting->warninger_name];
        }
        return $rs;
    }
}