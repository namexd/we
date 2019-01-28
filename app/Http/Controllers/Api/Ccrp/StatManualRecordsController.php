<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\StatManualRecordRequest;
use App\Models\Ccrp\CompanyDoesManualRecord;
use App\Models\Ccrp\Signature;
use App\Traits\ControllerUploader;
use App\Transformers\Ccrp\CoolerStatManualRecordsTransformer;
use Illuminate\Support\Facades\Storage;
use OSS\Core\OssException;

class StatManualRecordsController extends Controller
{
    Use ControllerUploader;

    public function create()
    {
        $this->check();
        $company = $this->company;
        if ($company->cdc_admin == 0 and $manual_records = $company->doesManualRecords) {
            $need_temp_record = !(bool)$manual_records->isDone->count();
            if ($need_temp_record) {
                $coolers = $company->coolersOnline->whereIn('cooler_type', CompanyDoesManualRecord::设备类型);
                return $this->response->collection($coolers, new CoolerStatManualRecordsTransformer())
                    ->addMeta('app', 'ccrp')
                    ->addMeta('unit_id', $this->company->id)
                    ->addMeta('action', 'sign');
            }
        }
        return $this->response->noContent();
    }

    public function store(StatManualRecordRequest $request)
    {
        $this->check();
        $records = $request->records;
        $oss_id = $request->sign_image_uniqid;
        print_r($oss_id);
        print_r($records);
        die();
    }


}
