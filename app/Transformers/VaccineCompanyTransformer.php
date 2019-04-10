<?php

namespace App\Transformers;

use App\Models\VaccineCompany;
use League\Fractal\TransformerAbstract;

class VaccineCompanyTransformer extends TransformerAbstract
{
    public function transform(VaccineCompany $vaccineCompany)
    {
        $rs = [
            'id' => $vaccineCompany->id,
            'name' => $vaccineCompany->name,
            'short' => $vaccineCompany->short,
            'full' => $vaccineCompany->full,
            'Category' => $vaccineCompany->Category,
            'created_at' => $vaccineCompany->created_at->toDateTimeString(),
            'updated_at' => $vaccineCompany->updated_at->toDateTimeString(),
        ];
        return  $rs;
    }
}