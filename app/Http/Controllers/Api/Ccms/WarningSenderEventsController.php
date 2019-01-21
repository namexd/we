<?php

namespace App\Http\Controllers\Api\Ccms;

use App\Http\Requests\Api\Ccms\WarningEventRequest;
use App\Models\Ccms\WarningSenderEvent;
use App\Transformers\Ccms\WarningEventTransformer;
use App\Transformers\Ccms\WarningSenderEventTransformer;

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
        return $this->response->item($event, new WarningSenderEventTransformer());
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
