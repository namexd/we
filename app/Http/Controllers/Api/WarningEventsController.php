<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\WarningEventRequest;
use App\Models\Ccms\WarningEvent;
use App\Transformers\WarningEventTransformer;
use DB;

class WarningEventsController extends Controller
{
    public function index($handled)
    {
        $this->check($this->user());

        $query2 =  DB::connection("dbyingyong")->table("warning_event")->whereIn('warning_event.company_id', $this->company_ids)
            ->leftJoin('collector', 'warning_event.collector_id', '=', 'collector.collector_id')
            ->select(DB::raw("id,handler_note, ck_warning_event.warning_type,ck_warning_event.collector_id,'0' as sender_id, temp_event,humi_event, warning_event_time AS event_time, handled, handled_time, handler, ck_warning_event.company_id, ck_warning_event.category_id,cooler_name as device_name,CONCAT('探头：',collector_name,'(',supplier_collector_id,')') as device_id"));
        $query1 =  DB::connection("dbyingyong")->table("warning_sender_event")->whereIn('warning_sender_event.company_id', $this->company_ids)
            ->leftJoin('sender', 'warning_sender_event.sender_id', '=', 'sender.sender_id')
            ->select(DB::raw("logid as id,handler_note, '5' AS warning_type,'0' as collector_id,ck_warning_sender_event.sender_id, '-' as temp_event , '-' as humi_event,sensor_event_time AS event_time, handled, handled_time, handler,ck_warning_sender_event.company_id, '0' as category_id,note as device_name,CONCAT('中继主机：',ck_warning_sender_event.sender_id) as device_id"));
        switch ($handled) {
            case 'unhandled':
                $evnets = WarningEvent::whereIn('company_id', $this->company_ids)->where('handled',0)->orderBy('id','desc')->paginate(10);
                break;
            case 'handled':
                $evnets = WarningEvent::whereIn('company_id', $this->company_ids)->where('handled',1)->orderBy('id','desc')->paginate(10);
                break;
            default  :
                $query = $query1->union($query2);
                $querySql = $query->toSql();
                $evnets = DB::connection("dbyingyong")->table(DB::raw("($querySql) as a"))->mergeBindings($query)
                    ->orderBy('event_time','desc')->paginate(10);

        }
        return $this->response->paginator($evnets, new WarningEventTransformer());

    }

    public function show($event)
    {
        $this->check($this->user());
        $event = WarningEvent::whereIn('company_id',$this->company_ids)->find($event);
        return $this->response->item($event, new WarningEventTransformer());
    }

    public function update(WarningEventRequest $request, $event)
    {
        $this->check($this->user());
        $event = WarningEvent::whereIn('company_id',$this->company_ids)->find($event);
        $event->handled = 1 ;
        $event->handler = $request ->handler;
        $event->handler_note = $request ->handler_note;
        $event->handled_time = time();
        $event->save();
        return $this->response->item($event, new WarningEventTransformer());
    }

}
