<?php

namespace App\Transformers;

use App\Models\EquipmentChangeApply;
use App\Models\EquipmentChangeDetail;
use App\Transformers\Ccrp\CoolerTransformer;
use League\Fractal\TransformerAbstract;


class EquipmentChangeDetailTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['cooler'];

    public function transform(EquipmentChangeDetail $detail)
    {
        $rs = [
            'id' => $detail->id,
            'apply_id' => $detail->apply_id,
            'cooler_id' => $detail->cooler_id,
            'change_type' => $detail->change_type,
            'change_type_name' => EquipmentChangeApply::CHANGE_TYPE[$detail->change_type],
            'reason' => $detail->reason,
        ];
        return $rs;
    }

    public function includeCooler(EquipmentChangeDetail $detail)
    {
        return $this->item($detail->cooler, new CoolerTransformer());
    }
}