<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\CollectorSyncRequest;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\DataHistory;
use App\Transformers\Ccrp\CollectorHistoryTransformer;
use App\Transformers\Ccrp\CollectorRealtimeTransformer;
use App\Transformers\Ccrp\CollectorTransformer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CollectorsController extends Controller
{

    public function index()
    {
        $this->check();
        $collectors = Collector::whereIn('company_id', $this->company_ids)->where('status', 1)->with('company')
            ->orderBy('company_id', 'asc')->orderBy('collector_name', 'asc')->paginate($this->pagesize);

        return $this->response->paginator($collectors, new CollectorTransformer());
    }


    public function history($collector)
    {
        $this->check();
        $start = request()->start ?? date('Y-m-d H:i:s',time()-4*3600);
        $end = request()->end ?? date('Y-m-d 23:59:59',strtotime($start));
        $start_time = strtotime($start);
        $end_time = strtotime($end);
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
