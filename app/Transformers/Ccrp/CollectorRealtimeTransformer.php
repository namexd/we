<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Collector;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CollectorRealtimeTransformer extends TransformerAbstract
{
    public function transform(Collector $collector)
    {
        $rs = [
            'id' => $collector->collector_id,
            'sn' => $collector->supplier_collector_id,
            'name' => $collector->collector_name,
            'cooler_id' => $collector->cooler_id,
            'cooler_name' => $collector->cooler_name,
            'company_id' => $collector->company_id,
            'company' => $collector->company->title,
            'temp' => round($collector->temp, 1),
            'humi' => round($collector->humi, 1),
            'refresh_time' => $collector->refresh_time,
        ];
        $rs['unnormal_status'] = $collector->unnormal_status;
        return $rs;
    }
}