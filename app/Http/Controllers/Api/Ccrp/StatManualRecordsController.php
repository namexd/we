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
                return $this->response->collection($coolers, new CoolerStatManualRecordsTransformer());
            }
        }
        return $this->response->noContent();
    }

    public function store(StatManualRecordRequest $request)
    {
        $this->check();

        $records = $request->records;
        print_r($records);
        die();
        $file = $request->file('sign');
        $folder = 'sign';
        $company_id = $this->company->id;
        $upload = $this->upload($file, $folder, $company_id);
        if ($upload['status'] == true) {
            print_r($upload);
            die();
            $data['records'] = $records;
            //add signature
            $signature = new Signature();
            $signature->sign_time = time();
            $signature->company_id = $company_id;
            $signature->file_uniqid = $upload['uniqid'];
            $signature->save();
            //add to records

        }
        print_r($upload['status']);
        die();
    }


}
