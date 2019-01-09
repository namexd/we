<?php

namespace App\Http\Controllers\Api;

use App\Models\Ccms\Cooler;
use App\Transformers\CoolerHistoryTransformer;
use App\Transformers\CoolerTransformer;
use Request;

class CoolersController extends Controller
{
    public function index()
    {
        $this->check($this->user());
        $coolers = Cooler::whereIn('company_id',$this->company_ids)->where('status',1)->with('company')
            ->orderBy('company_id','asc') ->orderBy('cooler_name','asc')->get();
        return $this->response->collection($coolers, new CoolerTransformer());
    }

    public function show($cooler)
    {
        $this->check($this->user());
        $cooler = Cooler::whereIn('company_id',$this->company_ids)->find($cooler);
        return $this->response->item($cooler, new CoolerTransformer());
    }

    public function history($cooler)
    {
        $this->check($this->user());
        $start = request()->start ?? date('Y-m-d H:i:s',time()-4*3600);
        $end = request()->end ?? date('Y-m-d 23:59:59',strtotime($start));
        $start_time = strtotime($start);
        $end_time = strtotime($end);

        $cooler = Cooler::whereIn('company_id',$this->company_ids)->with('collectors')->find($cooler);
        $data = $cooler->history($start_time,$end_time);
        return $this->response->item($data, new CoolerHistoryTransformer());
    }

}
