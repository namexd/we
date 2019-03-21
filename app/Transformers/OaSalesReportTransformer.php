<?php

namespace App\Transformers;

use App\Models\OaSalesReport;
use League\Fractal\TransformerAbstract;

class OaSalesReportTransformer extends TransformerAbstract
{
    public function transform(OaSalesReport $oaSalesReport )
    {
        $rs = [
            'user_id'=>$oaSalesReport->user_id,
            'customer_name'=>$oaSalesReport->customer_name,
            'customer_address'=>$oaSalesReport->customer_address,
            'contact'=>$oaSalesReport->contact,
            'phone'=>$oaSalesReport->phone,
            'program_type'=>join(',', array_map(function ($value){
                return $value['name'];
            },$oaSalesReport->programTypes->toArray())),
            'program_process'=>$oaSalesReport::PROGRAM_PROCESSES[$oaSalesReport->program_process],
            'program_goods'=>$oaSalesReport->program_goods,
            'program_amount'=>$oaSalesReport->program_amount,
            'process_note'=>$oaSalesReport->process_note,
            'need_help'=>$oaSalesReport->need_help,
            'status'=>$oaSalesReport->status,
            'created_at' => $oaSalesReport->created_at->toDateTimeString(),
            'updated_at' => $oaSalesReport->updated_at->toDateTimeString(),
        ];
        return  $rs;
    }
}