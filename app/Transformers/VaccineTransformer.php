<?php

namespace App\Transformers;

use App\Models\Vaccine;
use League\Fractal\TransformerAbstract;

class VaccineTransformer extends TransformerAbstract
{
    public function transform(Vaccine $vaccine)
    {
        $rs = [
            'id' => $vaccine->id,
            'name' => $vaccine->name,
            'short' => $vaccine->short,
            'full' => $vaccine->full,
            'created_at' => $vaccine->created_at->toDateTimeString(),
            'updated_at' => $vaccine->updated_at->toDateTimeString(),
        ];
        return  $rs;
    }
}