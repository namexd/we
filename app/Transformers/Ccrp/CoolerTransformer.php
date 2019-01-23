<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Cooler;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['collectors'];

    public function transform(Cooler $cooler)
    {
        return [
            'id' => $cooler->cooler_id,
            'cooler_sn' => $cooler->cooler_sn,
            'cooler_name' => $cooler->cooler_name,
            'brand' => $cooler->cooler_brand,
            'model' => $cooler->cooler_model,
            'size' => $cooler->cooler_size,
            'size2' => $cooler->cooler_size2,
            'company_id' => $cooler->company_id,
            'company' => $cooler->company->title??'',
            'created_at' => $cooler->install_time > 0 ? Carbon::createFromTimestamp($cooler->install_time)->toDateTimeString() : 0,
            'updated_at' => $cooler->update_time > 0 ? Carbon::createFromTimestamp($cooler->update_time)->toDateTimeString() : 0,
        ];
    }

    public function includeCollectors(Cooler $cooler)
    {
        return $this->collection($cooler->collectorsOnline, new CollectorIncludeTransformer());
    }

}