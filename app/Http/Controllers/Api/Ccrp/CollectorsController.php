<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\CollectorSyncRequest;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\DataHistory;
use App\Traits\ControllerDataRange;
use App\Transformers\Ccrp\CollectorHistoryTransformer;
use App\Transformers\Ccrp\CollectorRealtimeTransformer;
use App\Transformers\Ccrp\CollectorTransformer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CollectorsController extends Controller
{
    use ControllerDataRange;
    public $default_date = '今日';

    public function index()
    {
        $this->check();
        $collectors = Collector::whereIn('company_id', $this->company_ids)->where('status', 1)->with('company')
            ->orderBy('company_id', 'asc')->orderBy('collector_name', 'asc')->paginate($this->pagesize);

        return $this->response->paginator($collectors, new CollectorTransformer());
    }


    public function history($collector)
    {

        $this->set_default_datas($this->default_date);
        $this->check();
        if(request()->date_range)
        {
            $dates = $this->get_dates();
            $start_time = $dates['date_start'];
            $end_time = $dates['date_end'];
        }elseif(request()->start and request()->end){
            $start = request()->start ?? date('Y-m-d H:i:s',time()-4*3600);
            $end = request()->end ?? date('Y-m-d 23:59:59',strtotime($start));
            $start_time = strtotime($start);
            $end_time = strtotime($end);
        }else{
            $dates = $this->get_dates();
            $start_time = $dates['date_start'];
            $end_time = $dates['date_end'];
        }
        $collector = Collector::whereIn('company_id',$this->company_ids)->where('collector_id',$collector)->first();
        if($collector)
        {
            $data = $collector->history($start_time,$end_time);
            return $this->response->collection($data, new CollectorHistoryTransformer());
        }else{
            return $this->response->noContent();
        }
    }
//
//    public function realtime()
//    {
//        $this->check();
//        $collectors = Collector::whereIn('company_id', $this->company_ids)->where('status', 1)
//            ->orderBy('company_id', 'asc')->orderBy('collector_name', 'asc')->paginate(10);
//        return $this->response->paginator($collectors, new CollectorRealtimeTransformer());
//    }

}
