<?php

namespace App\Http\Controllers\Api\Ccms;

use App\Http\Requests\Api\Ccms\CollectorSyncRequest;
use App\Models\Ccms\Collector;
use App\Models\Ccms\Company;
use App\Models\Ccms\DataHistory;
use App\Transformers\Ccms\CollectorHistoryTransformer;
use App\Transformers\Ccms\CollectorRealtimeTransformer;
use App\Transformers\Ccms\CollectorTransformer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CollectorsController extends Controller
{

    public function index()
    {
        $this->check();
        $collectors = Collector::whereIn('company_id', $this->company_ids)->where('status', 1)->with('company')
            ->orderBy('company_id', 'asc')->orderBy('collector_name', 'asc')->get();

        return $this->response->collection($collectors, new CollectorTransformer());
    }

    public function realtime()
    {
        $this->check();
        $collectors = Collector::whereIn('company_id', $this->company_ids)->where('status', 1)
            ->orderBy('company_id', 'asc')->orderBy('collector_name', 'asc')->get();
        return $this->response->collection($collectors, new CollectorRealtimeTransformer());
    }

}
