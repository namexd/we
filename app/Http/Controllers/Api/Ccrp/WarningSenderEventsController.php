<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\WarningEventRequest;
use App\Models\Ccrp\WarningSenderEvent;
use App\Transformers\Ccrp\WarningEventTransformer;
use App\Transformers\Ccrp\WarningSenderEventTransformer;

class WarningSenderEventsController extends Controller
{
    public function index($handled)
    {
        $this->check();

        switch ($handled) {
            case 'unhandled':
                $evnets = WarningSenderEvent::whereIn('company_id', $this->company_ids)->where('handled',0)->orderBy('logid','desc')->paginate(10);

                break;
            case 'handled':
                $evnets = WarningSenderEvent::whereIn('company_id', $this->company_ids)->where('handled',1)->orderBy('logid','desc')->paginate(10);

                break;
            default  :
                $evnets = WarningSenderEvent::whereIn('company_id', $this->company_ids)->orderBy('handled','asc')->orderBy('logid','desc')->paginate(10);

        }
        return $this->response->paginator($evnets, new WarningSenderEventTransformer());

    }

    public function show($event)
    {
        $this->check();
        $event = WarningSenderEvent::whereIn('company_id',$this->company_ids)->find($event);
        return $event?$this->response->item($event, new WarningSenderEventTransformer()):$this->response->noContent();
    }

    public function update(WarningEventRequest $request, $event)
    {
        $this->check();
        $event = WarningSenderEvent::whereIn('company_id',$this->company_ids)->find($event);
        $event->handled = 1 ;
        $event->handler = $request ->handler;
        $event->handler_note = $request ->handler_note;
        $event->handled_time = time();
        $event->save();
        return $this->response->item($event, new WarningSenderEventTransformer());
    }

}