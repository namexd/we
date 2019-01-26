<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\StatManualRecordRequest;
use App\Models\Ccrp\CompanyDoesManualRecord;
use App\Transformers\Ccrp\CoolerStatManualRecordsTransformer;

class StatManualRecordsController extends Controller
{
    public function create()
    {
        $this->check();
        $company = $this->company;
        if ($company->cdc_admin == 0 and $manual_records = $company->doesManualRecords) {
            $need_temp_record = !(bool)$manual_records->isDone->count();
            if($need_temp_record)
            {
                $coolers = $company->coolersOnline->whereIn('cooler_type',CompanyDoesManualRecord::设备类型);
                return $this->response->collection($coolers,new CoolerStatManualRecordsTransformer());
            }
        }
        return $this->response->noContent();
    }

    public function store(StatManualRecordRequest $request)
    {
        $data = $request->records;
        $sign = $request->sign;
        dump($data);
        dd($sign);
    }




}
