<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\WarningSendlog;
use App\Transformers\Ccrp\WarningSendlogTransformer;

class WarningSendlogController extends Controller
{
    public function index($type = 'index')
    {
        $this->check();
        $model = WarningSendlog::whereIn('company_id', $this->company_ids);

        switch ($type) {
            case  'index':
                break;
            case  'overtemp':
                $model->where('event_type', '超温报警');
                break;
            case  'poweroff':
                $model->whereIn('event_type', ['市电断电' , '市电上电']);
                break;
            case  'offline':
                $model->where('event_type', '离线报警');
                break;
            default :
                break;
        }
        $logs = $model->orderBy('id', 'desc')->paginate(10);
        return $this->response->paginator($logs, new WarningSendlogTransformer());
    }

    public function show(WarningSendlog $sendlog)
    {
        $this->check();
        return $this->response->item($sendlog, new WarningSendlogTransformer());
    }


}
