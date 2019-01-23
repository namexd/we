<?php

namespace App\Http\Controllers\Api\Ccms;

use App\Http\Requests\Api\Ccms\WarningEventRequest;
use App\Models\Ccms\WarningEvent;
use App\Models\Ccms\WarningSenderEvent;
use App\Transformers\Ccms\WarningAllEventTransformer;
use App\Transformers\Ccms\WarningEventTransformer;
use App\Transformers\Ccms\WarningSenderEventTransformer;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;

class WarningEventsController extends Controller
{
    const TYPE =[
        1=>'高温报警',
        2=>'低温报警',
        3=>'高湿度报警',
        4=>'低湿度报警',
        5=>'断电报警',
        6=>'低压报警',
        7=>'高压报警'
    ];
    public function index($handled)
    {
        $this->check();

        switch ($handled) {
            case 'unhandled':
                $evnets = WarningEvent::whereIn('company_id', $this->company_ids)->where('handled',0)->orderBy('id','desc')->paginate(10);

                break;
            case 'handled':
                $evnets = WarningEvent::whereIn('company_id', $this->company_ids)->where('handled',1)->orderBy('id','desc')->paginate(10);

                break;
            default  :
                $evnets = WarningEvent::whereIn('company_id', $this->company_ids)->orderBy('handled','asc')->orderBy('id','desc')->paginate(10);

        }
        return $this->response->paginator($evnets, new WarningEventTransformer());

    }

    public function show($event)
    {
        $this->check();
        $event = WarningEvent::whereIn('company_id',$this->company_ids)->find($event);
        return $this->response->item($event, new WarningEventTransformer());
    }

    public function update(WarningEventRequest $request, $event)
    {
        $this->check();
        $event = WarningEvent::whereIn('company_id',$this->company_ids)->find($event);
        $event->handled = 1 ;
        $event->handler = $request ->handler;
        $event->handler_note = $request ->handler_note;
        $event->handled_time = time();
        $event->save();
        return $this->response->item($event, new WarningEventTransformer());
    }

}
