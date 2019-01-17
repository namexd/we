<?php

namespace App\Http\Controllers\Api\Ccms;

use App\Models\Ccms\Cooler;
use App\Transformers\Ccms\CoolerHistoryTransformer;
use App\Transformers\Ccms\CoolerTransformer;
use Request;

class CoolersController extends Controller
{
    public function index()
    {
        $this->check();
        $coolers = Cooler::whereIn('company_id',$this->company_ids)->where('status',1)->with('company')
            ->orderBy('company_id','asc') ->orderBy('cooler_name','asc')->get();
        return $this->response->collection($coolers, new CoolerTransformer());
    }

    public function show($cooler)
    {
        $this->check();
        $cooler = Cooler::whereIn('company_id',$this->company_ids)->find($cooler);
        return $this->response->item($cooler, new CoolerTransformer());
    }

    public function history($cooler)
    {
        $this->check();
        $start = request()->start ?? date('Y-m-d H:i:s',time()-4*3600);
        $end = request()->end ?? date('Y-m-d 23:59:59',strtotime($start));
        $start_time = strtotime($start);
        $end_time = strtotime($end);

        $cooler = Cooler::whereIn('company_id',$this->company_ids)->with('collectors')->find($cooler);
        $data = $cooler->history($start_time,$end_time);
        return $this->response->item($data, new CoolerHistoryTransformer());
    }

}
