<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\PrinterRequest;
use App\Models\Ccrp\Printer;
use App\Models\Ccrp\Vehicle;

use App\Transformers\Ccrp\PrinterTransformer;


class PrintersController extends Controller
{
    private $printer;

    public function __construct(Printer $printer)
    {
        $this->printer = $printer;
    }

    public function index()
    {
        $this->check();
        $vehicles = $this->printer->whereIn('company_id', $this->company_ids)->where('status', 1)
            ->paginate($this->pagesize);
        $transform= new PrinterTransformer();
        return $this->response->paginator($vehicles,$transform);
    }

    public function printTemp(PrinterRequest $request,Vehicle $vehicleModel)
    {

        $this->check();
        if (!($request->has('id')||$request->has('vehicle')))
        {
            return $this->response->error('请输入车牌号或者车辆id',422);
        }
        if ($request->has('start') and $request->has('end')) {
            $start = $request->start;
            $end = $request->end;
        } else {
            $start = date('Y-m-d H:i:s', time() - 3600);
            $end = date('Y-m-d H:i:s', time());
        }
        $lists=$vehicleModel->vehicle_temp($request->all(),$start,$end);
        $vehicle=$request->has('vehicle')?$request->vehicle:$vehicleModel->find($request->id)->vehicle;
        $id=$request->printer_id??1;
        $from=array();
        $from['from_type'] = 'vehicle/temp';
        $from['from_device'] = $vehicle;
        $from['from_order_id'] = 0;
        $from['from_time_begin'] = strtotime($start);
        $from['from_time_end'] = strtotime($end);

        //大于60条，拆分成多条打印
        $count_arr = count($lists);
        if($count_arr>60){
            $big_arr = array_chunk($lists, 60);

            $pages = count($big_arr);
            $pagei=0;
            foreach($big_arr as $data_arr_i){

                //from 增加分页
                $from['pages'] = $pages;
                $from['pagei'] = ++$pagei;

                $resp[]=$this->printer->printer_print_array($id,$vehicle,$this->printer->vehicle_print_data_format($vehicle,$data_arr_i,$this->company->id),$this->user()->id,null,$from);
            }
        }
        return $this->response->array($resp);
    }
}
