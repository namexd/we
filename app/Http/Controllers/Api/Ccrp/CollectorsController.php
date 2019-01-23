<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\CollectorSyncRequest;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\DataHistory;
use App\Transformers\Ccrp\CollectorHistoryTransformer;
use App\Transformers\Ccrp\CollectorRealtimeTransformer;
use App\Transformers\Ccrp\CollectorTransformer;
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
