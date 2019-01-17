<?php

namespace App\Http\Controllers\Api\Ccms;

use App\Http\Requests\Api\CollectorSyncRequest;
use App\Models\Ccms\DataHistory;
use App\Transformers\CollectorHistoryTransformer;
use App\Transformers\CollectorRealtimeTransformer;
use App\Transformers\CollectorTransformer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CollectorsController extends Controller
{

    public function index()
    {
        $this->check($this->user());
        $collectors = Collector::whereIn('company_id', $this->company_ids)->where('status', 1)->with('company')
            ->orderBy('company_id', 'asc')->orderBy('collector_name', 'asc')->get();

        return $this->response->collection($collectors, new CollectorTransformer());
    }

    public function realtime()
    {
        $this->check($this->user());
        $collectors = Collector::whereIn('company_id', $this->company_ids)->where('status', 1)
            ->orderBy('company_id', 'asc')->orderBy('collector_name', 'asc')->get();
        return $this->response->collection($collectors, new CollectorRealtimeTransformer());
    }

    /**
     * 目前支持1个探头
     * @param CollectorSyncRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function sync(CollectorSyncRequest $request)
    {
        $is_local = 0; //是否连接本地库
        $this->check($this->user());
        $data_history = new DataHistory();
        $sn = trim($request->sn);
        $data_id = trim($request->data_id);
        try {
            if ($is_local == 1) {
                //本地库
                $data = $data_history->setTable('lw_' . $sn . '')->where('data_id', '>', $data_id)->limit(100)->get();
            } else {
                //主库
                $data = $data_history->setTable('sensor.' . $sn . '')->where('data_id', '>', $data_id)->select(['sensor_id', 'data_id', 'temp', 'humi', 'sensor_collect_time as collect_time', 'system_time'])->limit(100)->get();
            }
        } catch (QueryException $e) {
            return $e->getMessage();
//                    $this->response->collection($e);
        }

        return $this->response->collection($data, new CollectorHistoryTransformer());

    }

    /**
     * 目前支持2个探头
     * @return \Dingo\Api\Http\Response
     */
    public function syncs(CollectorSyncRequest $request)
    {
        $this->check($this->user());

        if ($collectors = $request->collectors) {
            $data_history = new DataHistory();

            if (count($collectors) == 1) {
                $collector = json_decode($collectors[0]);
                $sn = trim($collector->sn);
                $data = $data_history->setTable('sensor.' . $sn . '')->where('data_id', '>', $collector->data_id)->select(['sensor_id', 'data_id', 'temp', 'humi', 'sensor_collect_time', 'system_time'])->get();
            } else {
                $i = 1;
                foreach ($collectors as $collector) {
                    $collector = json_decode($collector);
                    $sn = trim($collector->sn);
                    if ($i < count($collectors)) {
                        $sql[$i] = $data_history->setTable('sensor.' . $sn . '')->where('data_id', '>', $collector->data_id)->select(['sensor_id', 'data_id', 'temp', 'humi', 'sensor_collect_time', 'system_time']);
                        $i++;
                    } else {
                        $query = $data_history->setTable('sensor.' . $sn . '')->where('data_id', '>', $collector->data_id)->select(['sensor_id', 'data_id', 'temp', 'humi', 'sensor_collect_time', 'system_time']);
                        foreach ($sql as $q) {
                            $query->union($q);
                        }
                    }
                    $i++;
                }
                $data = $query->get();
            }
        }

        return $this->response->collection($data, new CollectorHistoryTransformer());

    }
}
