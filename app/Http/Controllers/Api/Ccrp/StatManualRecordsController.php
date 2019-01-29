<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\StatManualRecordRequest;
use App\Models\Ccrp\CompanyDoesManualRecord;
use App\Models\Ccrp\Signature;
use App\Models\Ccrp\StatManualRecord;
use App\Traits\ControllerUploader;
use App\Transformers\Ccrp\CoolerStatManualRecordsTransformer;
use Illuminate\Support\Facades\Storage;
use OSS\Core\OssException;

class StatManualRecordsController extends Controller
{
    Use ControllerUploader;

    public function index($month = null)
    {
        $this->check();
        $list = StatManualRecord::getListByMonth($this->company->id,$month);
        $data = ['data' => $list];
        return $this->response->array($data);
    }

    public function show($year, $month, $day, $session)
    {
        $this->check();
        $list = StatManualRecord::getListByMonth($this->company->id, $year, $month, $day, $session);
        $data = ['data' => $list];
        return $this->response->array($data);
    }

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
        $company_id = $this->company->id;
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $sign_time_a = date('a');
        $signature = new Signature();
        $signature->file_uniqid = $oss_id;
        $signature->company_id = $company_id;
        $signature->sign_time = time();
        $signature->save();
        foreach ($records as $record) {
            $stat_month = new StatManualRecord();
            $stat_month->company_id = $company_id;
            $stat_month->year = $year;
            $stat_month->month = $month;
            $stat_month->day = $day;
            $stat_month->cooler_id = $record['cooler_id'];
            $stat_month->cooler_name = $record['cooler_name'];
            $stat_month->cooler_sn = $record['cooler_sn'];
            $stat_month->cooler_type = $record['cooler_type'];
            $stat_month->temp_cool = $record['temp_cool'];
            $stat_month->temp_cold = $record['temp_cold'];
            $stat_month->sign_note = $record['sign_note'];
            $stat_month->sign_id = $signature->id;
            $stat_month->sign_time = $signature->sign_time;
            $stat_month->sign_time_a = $sign_time_a;
            $stat_month->create_time = time();
            $stat_month->save();
        }
        $this->response->created();
    }


}
