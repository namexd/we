<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\WarningEventRequest;
use App\Models\Ccrp\WarningSenderEvent;
use App\Traits\ControllerDataRange;
use App\Transformers\Ccrp\WarningEventTransformer;
use App\Transformers\Ccrp\WarningSenderEventTransformer;

class WarningSenderEventsController extends Controller
{
    use ControllerDataRange;
    public function __construct()
    {
        parent::__construct();
        $this->set_default_datas(request()->date_name??'最近30天');
    }
    public function index($handled)
    {
        $this->check();

        switch ($handled) {
            case 'unhandled':
                $evnets = WarningSenderEvent::whereIn('company_id', $this->company_ids)->where('handled',0)->orderBy('logid','desc')->paginate($this->pagesize);
                return $this->response->paginator($evnets, new WarningSenderEventTransformer());
                break;
            case 'handled':
                $model = WarningSenderEvent::whereIn('company_id', $this->company_ids)->where('handled',1);
                $model= $model->whereBetween(WarningSenderEvent::TIME_FIELD,$this->get_dates());
                $evnets = $model->orderBy('logid','desc')->paginate($this->pagesize);
                return $this->response->paginator($evnets, new WarningSenderEventTransformer())->addMeta('date_range',$this->get_dates('datetime',true));
                break;
            default  :
                $model = WarningSenderEvent::whereIn('company_id', $this->company_ids);
                $model = $model->whereBetween(WarningSenderEvent::TIME_FIELD,$this->get_dates());
                $evnets = $model->orderBy('logid','desc')->paginate($this->pagesize);
                return $this->response->paginator($evnets, new WarningSenderEventTransformer())->addMeta('date_range',$this->get_dates('datetime',true));
        }

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
